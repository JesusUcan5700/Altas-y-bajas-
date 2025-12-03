/**
 * Protección de marca de agua del desarrollador
 * Sistema codificado por Juan Ucan
 * No modificar ni eliminar
 */
(function() {
    'use strict';
    
    var watermarkText = 'Sistema codificado por Juan Ucan';
    var watermarkId = 'dev-signature';
    
    function createWatermark() {
        var existing = document.getElementById(watermarkId);
        if (existing) {
            existing.textContent = watermarkText;
            return existing;
        }
        
        var watermark = document.createElement('div');
        watermark.id = watermarkId;
        watermark.className = 'dev-watermark';
        watermark.textContent = watermarkText;
        watermark.style.cssText = `
            position: fixed !important;
            bottom: 25px !important;
            right: 20px !important;
            font-size: 13px !important;
            color: rgba(0, 0, 0, 0.7) !important;
            z-index: 9999 !important;
            pointer-events: none !important;
            user-select: none !important;
            font-family: 'Courier New', monospace !important;
            text-shadow: 1px 1px 3px rgba(255,255,255,0.9) !important;
            transform: rotate(-3deg) !important;
            background: rgba(255,255,255,0.9) !important;
            padding: 4px 8px !important;
            border-radius: 5px !important;
            opacity: 0.7 !important;
            font-weight: 600 !important;
            letter-spacing: 0.8px !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
            box-shadow: none !important;
            will-change: transform !important;
            backface-visibility: hidden !important;
        `;
        
        document.body.appendChild(watermark);
        return watermark;
    }
    
    function protectWatermark() {
        var watermark = createWatermark();
        
        // Observador de mutaciones para detectar cambios
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.removedNodes.forEach(function(node) {
                        if (node.id === watermarkId || (node.className && node.className.includes('dev-watermark'))) {
                            setTimeout(createWatermark, 100);
                        }
                    });
                }
                
                if (mutation.type === 'attributes' && mutation.target.id === watermarkId) {
                    setTimeout(function() {
                        createWatermark();
                    }, 100);
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style', 'class', 'id']
        });
        
        // Protección contra inspección de elementos
        watermark.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Una sola verificación periódica más eficiente
        setInterval(function() {
            var wm = document.getElementById(watermarkId);
            if (!wm || wm.textContent !== watermarkText) {
                createWatermark();
            }
        }, 10000);
    }
    
    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', protectWatermark);
    } else {
        protectWatermark();
    }
    
    // También ejecutar cuando la página se muestre (navegación atrás/adelante)
    window.addEventListener('pageshow', protectWatermark);
    
})();
