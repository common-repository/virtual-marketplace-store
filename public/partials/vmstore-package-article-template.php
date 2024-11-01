<article id="package-{{post_id}}" class="package-{{post_id}} vmstore-package type-vmstore-package status-publish hentry entry">

    <div class="vmpackage-section-header-wrapper">
        <div class="vmpackage-section package-header">
            {{vmp_package_header}}
        </div>
        
        <header class="vmpackage-section">
            <div class="package-header-content-container">
                <div class="vmstore-package-header-icon{{vmp_icon_classes}}" data-title="{{vmp_icon_title}}"{{vmp_icon_style}}></div>
                <div class="entry-title-wrapper">
                    <h1>{{vmp_title}}</h1>
                    <div class="entry-subtitle">{{vmp_tagline}}</div>
                    <div class="entry-tags">{{vmp_tags}}</div>
                </div>
            </div>
            <div class="package-header-sidebar-container">
                <div class="vmstore-package-actions">
                    <div class="package-cta-container">
                        {{vmp_cta}}
                    </div>
                    <div>Pricing</div>
                    <div class="package-pricing">
                        <div class="pricing-term">Starting at</div>
                        <div class="pricing-minimum">{{vmp_price}}</div>
                    </div>
                </div>
            </div>
        </header>
    </div>

    <div class="vmpackage-section-body-wrapper">
        <div class="vmpackage-section vmstore-product-headers">
            {{vmp_products_header}}
        </div>
        <div class="vmpackage-section vmstore-product-contents">
            {{vmp_products_content}}
        </div>
    </div>

    <footer class="vmpackage-section package-footer">
        {{vmp_package_footer}}
    </footer>
</article>