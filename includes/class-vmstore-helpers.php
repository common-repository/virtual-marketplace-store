<?php

class VmstoreHelpers
{
  public static function doHeimdall($payload=array(), $category="", $action="", $label="", $environment="PROD")
  {
    $url = "https://heimdall-api-prod.apigateway.co/heimdall.v1.Heimdall/CreateUserEvents";
    
    $config = get_option('vmstore_options');

    $partnerId = VmstoreHelpers::safeGet($config, "pid");
    $marketId = VmstoreHelpers::safeGet($config, "mid");
    $storeSlug = VmstoreHelpers::safeGet($config, "slug");

    $deploymentId = "virtual-marketplace-store-";
    if (defined('vmstore_version')) {
      $pluginVersion = constant('vmstore_version');
      $deploymentId .= $pluginVersion;
      $payload["pluginVersion"] = $pluginVersion;
    }

    $payload["siteUrl"] = get_home_url();
    $payload["partnerId"] = $partnerId;
    $payload["marketId"] = $marketId;
    $payload["storeUrl"] = get_home_url() . "/" . $storeSlug;

    $bodyArgs = json_encode(
      array("userEvents" =>
        array(
          array(
            "project" => "public-store-wordpress",
            "environment" => $environment,
            "deploymentId" => $deploymentId,
            "dimensions" => $payload,
            "traceId" => get_home_url(),
            "eventCategory" => $category,
            "eventAction" => $action,
            "eventLabel" => $label,
            "eventValue" => 1,
            "timestamp" => gmdate("Y-m-d\TH:i:s.000\Z")
          )
        )
      )
    );

    $args = array(
      'method' => 'POST',
      'headers' => array('content-type' => 'application/json'),
      'body' => $bodyArgs
    );

    VmstoreHelpers::doRPC($url, $args);
  }

  public static function doLog($obj) {
    error_log(json_encode($obj));
  }

  public static function formatUrl($url, $args)
  {
    $query = http_build_query($args);
    $query = preg_replace("/\%5B\d{1,}\%5D/", "", $query);
    return $url . $query;
  }

  public static function doRPC($url, $args, $success_callback=null, $error_callback=null)
  {
    $output = -1;
    $response = wp_remote_post($url, $args);

    $response_code = wp_remote_retrieve_response_code( $response );
    $response_body = wp_remote_retrieve_body( $response );

    if ( !in_array( $response_code, array(200,201) ) || is_wp_error( $response_body ) )
    {
      if (isset($error_callback))
      {
        $output = $error_callback($response);
      }
      else
      {
        $output = 0;
      }
    } else {
      if (isset($success_callback))
      {
        $output = $success_callback($response_body);
      }
      else
      {
        $output = 1;
      }
    }

    return $output;
  }

  public static function safeGet($object, $key)
  {
    if (isset($object[$key])) {
      return $object[$key];
    }
    return "";
  }
}