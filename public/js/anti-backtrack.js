/**
 * TracAdemics Anti-Backtrack Security Script
 * Prevents browser back button functionality and implements security measures
 */

(function() {
    'use strict';
    
    // Main anti-backtrack functionality
    const AntiBacktrack = {
        
        // Initialize all security measures
        init: function() {
            this.preventBackButton();
            this.disableShortcuts();
            this.preventContextMenu();
            this.clearBrowserData();
            this.handleVisibilityChange();
            this.preventCaching();
            console.log('TracAdemics Anti-Backtrack Security: Enabled');
        },
        
        // Prevent back button functionality
        preventBackButton: function() {
            // Push current state to prevent back navigation
            history.pushState(null, null, location.href);
            
            // Handle popstate event (back/forward buttons)
            window.onpopstate = function() {
                history.go(1);
            };
            
            // Alternative method for older browsers
            window.addEventListener('popstate', function(event) {
                history.pushState(null, null, location.href);
            });
        },
        
        // Disable keyboard shortcuts
        disableShortcuts: function() {
            document.addEventListener('keydown', function(e) {
                // F5 (Refresh)
                if (e.keyCode === 116) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                
                // Ctrl+R (Refresh)
                if (e.ctrlKey && e.keyCode === 82) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                
                // Ctrl+F5 (Hard Refresh)
                if (e.ctrlKey && e.keyCode === 116) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                
                // Alt+Left Arrow (Back)
                if (e.altKey && e.keyCode === 37) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                
                // Alt+Right Arrow (Forward)
                if (e.altKey && e.keyCode === 39) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                
                // Backspace (when not in input field)
                if (e.keyCode === 8 && !['INPUT', 'TEXTAREA'].includes(e.target.tagName)) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        },
        
        // Disable right-click context menu
        preventContextMenu: function() {
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });
        },
        
        // Clear browser data
        clearBrowserData: function() {
            if (typeof(Storage) !== 'undefined') {
                // Clear session storage
                sessionStorage.clear();
                
                // Clear specific localStorage items
                localStorage.removeItem('navigationData');
                localStorage.removeItem('pageHistory');
                localStorage.removeItem('userSession');
            }
        },
        
        // Handle visibility change (tab switching)
        handleVisibilityChange: function() {
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // Page is hidden - user switched tabs
                    console.log('TracAdemics Security: Page visibility changed - monitoring');
                } else {
                    // Page is visible again
                    history.pushState(null, null, location.href);
                }
            });
        },
        
        // Prevent page caching
        preventCaching: function() {
            // Clear cache on page load
            window.addEventListener('load', function() {
                if (typeof(Storage) !== 'undefined') {
                    sessionStorage.clear();
                }
            });
            
            // Clear cache before page unload
            window.addEventListener('beforeunload', function() {
                if (typeof(Storage) !== 'undefined') {
                    sessionStorage.clear();
                    localStorage.removeItem('navigationData');
                    localStorage.removeItem('pageHistory');
                }
            });
            
            // Handle page hide event
            window.addEventListener('pagehide', function() {
                if (typeof(Storage) !== 'undefined') {
                    sessionStorage.clear();
                }
            });
        },
        
        // Security check for authentication status
        checkAuthStatus: function() {
            // This can be customized based on your authentication logic
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) {
                console.warn('TracAdemics Security: CSRF token not found');
            }
        },
        
        // Disable developer tools (limited effectiveness)
        disableDevTools: function() {
            // Detect DevTools opening (not foolproof)
            let devtools = {
                open: false,
                orientation: null
            };
            
            setInterval(function() {
                if (window.outerHeight - window.innerHeight > 160 || 
                    window.outerWidth - window.innerWidth > 160) {
                    if (!devtools.open) {
                        devtools.open = true;
                        console.clear();
                    }
                } else {
                    devtools.open = false;
                }
            }, 500);
        }
    };
    
    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            AntiBacktrack.init();
        });
    } else {
        AntiBacktrack.init();
    }
    
    // Expose to global scope if needed
    window.TracAdemicsAntiBacktrack = AntiBacktrack;
    
})();