/**
 * Filename: selm-handler.js
 * Author: Krafty Sprouts Media, LLC
 * Created: 24/11/2025
 * Version: 2.2.3
 * Last Modified: 24/11/2025
 * Description: Smart External Link Manager - Vanilla JavaScript Handler
 * Optimized for performance and cache compatibility
 * Ported from KSM-ELM with enhancements
 * 
 * @since 2.1.0
 */

(function() {
    'use strict';
    
    // Performance optimizations
    let processed_links = new WeakSet();
    let current_host = '';
    let current_scheme = '';
    let observer = null;
    let processing_timeout = null;
    let config = {
        addIcon: false,
        iconType: 'svg',
        iconClass: 'selm-external-icon-svg',
        iconSvgFile: 'icon-external',
        iconPosition: 'after',
        addNofollow: true,
        addNoopener: true,
        excludeClasses: ['no-external'],
        excludeDomains: []
    };
    
    /**
     * Initialize the external link processor
     * Optimized for minimal DOM manipulation
     * 
     * @since 2.1.0
     */
    function selm_initialize() {
        // Get site data from localized script
        if (typeof selm_data !== 'undefined') {
            current_host = selm_data.site_host || '';
            current_scheme = selm_data.site_scheme || 'https';
            
            // Merge configuration if provided
            if (selm_data.config) {
                config = Object.assign({}, config, selm_data.config);
            }
        }
        
        // Process existing links immediately
        selm_process_external_links();
        
        // Set up optimized mutation observer for dynamic content
        selm_setup_observer();
    }
    
    /**
     * Process external links with performance optimizations
     * 
     * @since 2.1.0
     */
    function selm_process_external_links() {
        // Use querySelectorAll for better performance
        const links = document.querySelectorAll('a[href]');
        
        // Process in batches to avoid blocking UI
        let batch_size = 50;
        let current_batch = 0;
        
        function process_batch() {
            const start = current_batch * batch_size;
            const end = Math.min(start + batch_size, links.length);
            
            for (let i = start; i < end; i++) {
                const link = links[i];
                
                // Skip if already processed
                if (processed_links.has(link)) {
                    continue;
                }
                
                const href = link.getAttribute('href');
                if (!href) {
                    processed_links.add(link);
                    continue;
                }
                
                // Skip special links
                if (selm_is_special_link(href)) {
                    processed_links.add(link);
                    continue;
                }
                
                // Check exclusions
                if (selm_should_exclude_link(link)) {
                    processed_links.add(link);
                    continue;
                }
                
                // Check if external and process
                if (selm_is_external_link(href)) {
                    selm_make_external(link);
                }
                
                processed_links.add(link);
            }
            
            current_batch++;
            
            // Continue processing if there are more links
            if (end < links.length) {
                // Use requestAnimationFrame for smooth processing
                requestAnimationFrame(process_batch);
            }
        }
        
        // Start processing
        if (links.length > 0) {
            process_batch();
        }
    }
    
    /**
     * Make a link external with minimal DOM manipulation
     * 
     * @param {HTMLElement} link
     * @since 2.1.0
     */
    function selm_make_external(link) {
        // Set target to _blank
        link.setAttribute('target', '_blank');
        
        // Handle rel attribute efficiently
        const existing_rel = link.getAttribute('rel') || '';
        let rel_parts = existing_rel ? existing_rel.split(' ').filter(r => r) : [];
        
        if (config.addNoopener && rel_parts.indexOf('noopener') === -1) {
            rel_parts.push('noopener');
        }
        
        if (config.addNofollow && rel_parts.indexOf('nofollow') === -1) {
            rel_parts.push('nofollow');
        }
        
        if (rel_parts.length > 0) {
            link.setAttribute('rel', rel_parts.join(' '));
        }
        
        // Add external link class
        const existing_class = link.getAttribute('class') || '';
        if (existing_class.indexOf('selm-external-link') === -1) {
            link.setAttribute('class', (existing_class + ' selm-external-link').trim());
        }
        
        // Add icon if enabled
        if (config.addIcon) {
            selm_add_icon(link);
        }
    }
    
    /**
     * Add icon to external link
     * 
     * @param {HTMLElement} link
     * @since 2.1.0
     */
    function selm_add_icon(link) {
        // Check if icon already exists
        if (link.querySelector('.selm-external-icon')) {
            return;
        }
        
        let icon_html = '';
        
        switch (config.iconType) {
            case 'svg':
                icon_html = selm_get_svg_icon(config.iconSvgFile || 'icon-external', config.iconClass || 'selm-external-icon-svg');
                break;
            case 'fontawesome':
                icon_html = '<i class="' + selm_escape_html(config.iconClass) + ' selm-external-icon"></i>';
                break;
            case 'custom':
                // Custom icon HTML is passed from PHP
                if (config.customIcon) {
                    icon_html = '<span class="selm-external-icon selm-custom-icon">' + config.customIcon + '</span>';
                }
                break;
            case 'dashicon':
                // Deprecated: Dashicons support
                icon_html = '<span class="dashicons ' + selm_escape_html(config.iconClass) + ' selm-external-icon"></span>';
                break;
        }
        
        if (icon_html) {
            if (config.iconPosition === 'before') {
                link.insertAdjacentHTML('afterbegin', icon_html + ' ');
            } else {
                link.insertAdjacentHTML('beforeend', ' ' + icon_html);
            }
        }
    }
    
    /**
     * Get SVG icon HTML (placeholder - would need to fetch from server or embed)
     * In a real implementation, you'd either:
     * 1. Embed SVG content in the config
     * 2. Fetch SVG from server
     * 3. Use a predefined SVG string
     * 
     * @param {string} iconName Icon file name
     * @param {string} className CSS class
     * @returns {string} SVG HTML
     * @since 2.2.0
     */
    function selm_get_svg_icon(iconName, className) {
        // Default external link SVG icon
        const defaultSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20" height="20" class="selm-external-icon ' + selm_escape_html(className) + '"><path d="M14.89 13.09l-2.11 2.11c-.17.17-.38.31-.6.42-.22.11-.46.19-.7.23-.24.04-.49.05-.73.02-.24-.03-.48-.1-.7-.2-.22-.1-.43-.23-.61-.39l-3.83-3.84c-.33-.33-.58-.73-.73-1.18-.15-.45-.19-.93-.12-1.4.07-.47.24-.92.49-1.33.25-.41.58-.78.97-1.08l.87-.61c.06-.04.12-.09.19-.12.07-.03.15-.05.22-.07.08-.02.15-.03.23-.03.08 0 .16.01.24.03.08.02.15.04.22.07.07.03.13.08.19.12l.87.61c.39.3.72.67.97 1.08.25.41.42.86.49 1.33.07.47.03.95-.12 1.4-.15.45-.4.85-.73 1.18zm-1.77 1.76l2.11-2.11c.17-.17.31-.38.42-.6.11-.22.19-.46.23-.7.04-.24.05-.49.02-.73-.03-.24-.1-.48-.2-.7-.1-.22-.23-.43-.39-.61l-3.83-3.84c-.33-.33-.73-.58-1.18-.73-.45-.15-.93-.19-1.4-.12-.47.07-.92.24-1.33.49-.41.25-.78.58-1.08.97l-.61.87c-.04.06-.09.12-.12.19-.03.07-.05.15-.07.22-.02.08-.03.15-.03.23 0 .08.01.16.03.24.02.08.04.15.07.22.03.07.08.13.12.19l.61.87c.3.39.67.72 1.08.97.41.25.86.42 1.33.49.47.07.95.03 1.4-.12.45-.15.85-.4 1.18-.73l3.83-3.84c.16-.18.29-.39.39-.61.1-.22.17-.46.2-.7.03-.24.02-.49-.02-.73-.04-.24-.12-.48-.23-.7-.11-.22-.25-.43-.42-.6l-2.11-2.11c-.17-.17-.38-.31-.6-.42-.22-.11-.46-.19-.7-.23-.24-.04-.49-.05-.73-.02-.24.03-.48.1-.7.2-.22.1-.43.23-.61.39l-3.83 3.84c-.33.33-.58.73-.73 1.18-.15.45-.19.93-.12 1.4.07.47.24.92.49 1.33.25.41.58.78.97 1.08l.87.61c.06.04.12.09.19.12.07.03.15.05.22.07.08.02.15.03.23.03.08 0 .16-.01.24-.03.08-.02.15-.04.22-.07.07-.03.13-.08.19-.12l.87-.61c.39-.3.72-.67.97-1.08.25-.41.42-.86.49-1.33.07-.47.03-.95-.12-1.4-.15-.45-.4-.85-.73-1.18l-3.83-3.84c-.18-.16-.39-.29-.61-.39-.22-.1-.46-.17-.7-.2-.24-.03-.49-.02-.73.02-.24.04-.48.12-.7.23-.22.11-.43.25-.6.42l-2.11 2.11c-.17.17-.31.38-.42.6-.11.22-.19.46-.23.7-.04.24-.05.49-.02.73.03.24.1.48.2.7.1.22.23.43.39.61l3.83 3.84c.33.33.73.58 1.18.73.45.15.93.19 1.4.12.47-.07.92-.24 1.33-.49.41-.25.78-.58 1.08-.97l.61-.87c.04-.06.09-.12.12-.19.03-.07.05-.15.07-.22.02-.08.03-.15.03-.23 0-.08-.01-.16-.03-.24-.02-.08-.04-.15-.07-.22-.03-.07-.08-.13-.12-.19l-.61-.87c-.3-.39-.67-.72-1.08-.97-.41-.25-.86-.42-1.33-.49-.47-.07-.95-.03-1.4.12-.45.15-.85.4-1.18.73l-3.83 3.84c-.16.18-.29.39-.39.61-.1.22-.17.46-.2.7-.03.24-.02.49.02.73.04.24.12.48.23.7.11.22.25.43.42.6z"/><path d="M17 3h-8c-1.1 0-2 .9-2 2v1h2V5h8v10h-8v-1H7v1c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>';
        
        // For now, return default SVG. In production, you'd fetch the actual SVG based on iconName
        // or embed SVG content in the config from PHP
        return defaultSvg;
    }
    
    /**
     * Escape HTML to prevent XSS
     * 
     * @param {string} text
     * @returns {string}
     * @since 2.1.0
     */
    function selm_escape_html(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Check if link should be excluded
     * 
     * @param {HTMLElement} link
     * @returns {boolean}
     * @since 2.1.0
     */
    function selm_should_exclude_link(link) {
        // Check excluded classes
        if (config.excludeClasses && config.excludeClasses.length > 0) {
            const link_classes = link.getAttribute('class') || '';
            for (let i = 0; i < config.excludeClasses.length; i++) {
                if (link_classes.indexOf(config.excludeClasses[i]) !== -1) {
                    return true;
                }
            }
        }
        
        // Check excluded domains
        const href = link.getAttribute('href');
        if (config.excludeDomains && config.excludeDomains.length > 0 && href) {
            try {
                const url = new URL(href, window.location.href);
                const link_host = url.hostname.replace(/^www\./, '');
                
                for (let i = 0; i < config.excludeDomains.length; i++) {
                    const domain = config.excludeDomains[i].replace(/^www\./, '');
                    if (link_host === domain || link_host.endsWith('.' + domain)) {
                        return true;
                    }
                }
            } catch (e) {
                // Invalid URL, skip
            }
        }
        
        return false;
    }
    
    /**
     * Optimized external link detection
     * 
     * @param {string} href
     * @returns {boolean}
     * @since 2.1.0
     */
    function selm_is_external_link(href) {
        try {
            // Handle relative URLs quickly
            const first_char = href.charAt(0);
            if (first_char === '/' || first_char === '#' || first_char === '?') {
                return false;
            }
            
            // Handle protocol-relative URLs
            if (href.substring(0, 2) === '//') {
                href = current_scheme + ':' + href;
            }
            
            // Quick check for protocol
            if (!href.startsWith('http://') && !href.startsWith('https://')) {
                return false;
            }
            
            // Use URL constructor for parsing (modern browsers)
            const url = new URL(href);
            const link_host = url.hostname;
            
            // Normalize hosts for comparison
            const normalized_current = current_host.replace(/^www\./, '');
            const normalized_link = link_host.replace(/^www\./, '');
            
            return normalized_current !== normalized_link;
            
        } catch (e) {
            // If URL parsing fails, treat as internal
            return false;
        }
    }
    
    /**
     * Optimized special link detection
     * 
     * @param {string} href
     * @returns {boolean}
     * @since 2.1.0
     */
    function selm_is_special_link(href) {
        const first_char = href.charAt(0);
        
        // Quick check for hash links
        if (first_char === '#') {
            return true;
        }
        
        // Check for protocol-based special links
        const protocols = ['javascript:', 'mailto:', 'tel:', 'sms:', 'ftp:'];
        const lower_href = href.toLowerCase();
        
        return protocols.some(protocol => lower_href.startsWith(protocol));
    }
    
    /**
     * Set up optimized mutation observer
     * 
     * @since 2.1.0
     */
    function selm_setup_observer() {
        if (!window.MutationObserver) {
            return;
        }
        
        observer = new MutationObserver(function(mutations) {
            let has_new_links = false;
            
            // Check if any new links were added
            for (const mutation of mutations) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    for (const node of mutation.addedNodes) {
                        if (node.nodeType === 1) { // Element node
                            if (node.tagName === 'A' || (node.querySelector && node.querySelector('a'))) {
                                has_new_links = true;
                                break;
                            }
                        }
                    }
                    if (has_new_links) break;
                }
            }
            
            if (has_new_links) {
                // Debounce processing to avoid excessive calls
                if (processing_timeout) {
                    clearTimeout(processing_timeout);
                }
                processing_timeout = setTimeout(selm_process_external_links, 100);
            }
        });
        
        // Start observing with minimal overhead
        if (document.body) {
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }
    
    /**
     * Clean up resources
     * 
     * @since 2.1.0
     */
    function selm_cleanup() {
        if (observer) {
            observer.disconnect();
            observer = null;
        }
        if (processing_timeout) {
            clearTimeout(processing_timeout);
            processing_timeout = null;
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', selm_initialize);
    } else {
        selm_initialize();
    }
    
    // Clean up on page unload
    window.addEventListener('beforeunload', selm_cleanup);
    
})();

