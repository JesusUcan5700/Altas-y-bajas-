/**
 * Sistema de Búsqueda Avanzada para Tablas
 * Busca en TODOS los campos de la tabla incluyendo campos anidados
 */

class BuscadorAvanzado {
    constructor(inputId, tableBodyId, data = null) {
        this.inputElement = document.getElementById(inputId);
        this.tableBody = document.getElementById(tableBodyId);
        this.data = data;
        this.init();
    }
    
    init() {
        if (!this.inputElement) {
            console.error('Input de búsqueda no encontrado');
            return;
        }
        
        console.log('🔍 Buscador avanzado inicializado');
        
        // Evento de búsqueda con debounce para mejor rendimiento
        let timeout;
        this.inputElement.addEventListener('input', (e) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.buscar(e.target.value);
            }, 300); // Espera 300ms después de que el usuario deje de escribir
        });
        
        // Icono de limpiar búsqueda
        this.agregarBotonLimpiar();
        
        // Debug: Mostrar qué texto está capturando de la primera fila
        if (this.tableBody.rows.length > 0) {
            const primeraFila = this.tableBody.rows[0];
            const textoCapturado = this.obtenerTextoCompleto(primeraFila);
            console.log('📝 Ejemplo de texto capturado de la primera fila:', textoCapturado);
        }
    }
    
    buscar(termino) {
        termino = termino.toLowerCase().trim();
        const filas = this.tableBody.querySelectorAll('tr');
        let resultadosEncontrados = 0;
        
        // Dividir el término en palabras para búsqueda múltiple
        const palabras = termino.split(/\s+/).filter(p => p.length > 0);
        
        filas.forEach(fila => {
            // Saltar filas de "no hay datos" o "error"
            if (fila.cells.length === 1 && fila.querySelector('.text-center')) {
                return;
            }
            
            if (termino === '') {
                fila.style.display = '';
                fila.classList.remove('filtro-oculto', 'filtro-visible');
                fila.style.backgroundColor = '';
                return;
            }
            
            // Obtener todo el texto de la fila
            let textoCompleto = this.obtenerTextoCompleto(fila);
            
            // Buscar coincidencia - debe contener todas las palabras
            let coincide = palabras.every(palabra => textoCompleto.includes(palabra));
            
            if (coincide) {
                fila.style.display = '';
                fila.classList.add('filtro-visible');
                fila.classList.remove('filtro-oculto');
                resultadosEncontrados++;
                
                // Resaltar el término encontrado
                this.resaltarTermino(fila, termino);
            } else {
                fila.style.display = 'none';
                fila.classList.add('filtro-oculto');
                fila.classList.remove('filtro-visible');
                fila.style.backgroundColor = '';
            }
        });
        
        // Mostrar mensaje si no hay resultados
        this.mostrarMensajeResultados(resultadosEncontrados, termino);
    }
    
    obtenerTextoCompleto(fila) {
        let textos = [];
        
        // Recorrer todas las celdas excepto la de acciones y checkbox
        Array.from(fila.cells).forEach((celda, index) => {
            // Ignorar la columna de acciones (usualmente la última)
            if (celda.querySelector('.btn-group')) {
                return;
            }
            
            // Ignorar la columna de checkbox (primera columna)
            if (celda.querySelector('input[type="checkbox"]')) {
                return;
            }
            
            // Obtener todo el texto visible de la celda, incluyendo badges
            let texto = '';
            
            // Método 1: textContent (texto plano sin formato HTML)
            texto = celda.textContent || celda.innerText || '';
            
            // Método 2: Extraer texto de badges específicamente
            const badges = celda.querySelectorAll('.badge, .btn, span, small');
            badges.forEach(badge => {
                texto += ' ' + (badge.textContent || badge.innerText || '');
            });
            
            // Método 3: Incluir atributos data-* que puedan tener información
            Object.keys(celda.dataset).forEach(key => {
                texto += ' ' + celda.dataset[key];
            });
            
            // Método 4: Incluir atributos title y alt
            if (celda.title) texto += ' ' + celda.title;
            const imgs = celda.querySelectorAll('img');
            imgs.forEach(img => {
                if (img.alt) texto += ' ' + img.alt;
                if (img.title) texto += ' ' + img.title;
            });
            
            // Limpiar espacios múltiples y agregar
            texto = texto.replace(/\s+/g, ' ').trim();
            if (texto) {
                textos.push(texto);
            }
        });
        
        // Unir todos los textos y normalizar
        let textoFinal = textos.join(' ').toLowerCase().trim();
        
        // Remover caracteres especiales que puedan interferir
        textoFinal = textoFinal.replace(/[\n\r\t]+/g, ' ').replace(/\s+/g, ' ');
        
        return textoFinal;
    }
    
    resaltarTermino(fila, termino) {
        // Remover resaltado previo
        fila.querySelectorAll('.highlight-search').forEach(el => {
            el.outerHTML = el.textContent;
        });
        
        // Nota: El resaltado completo requeriría una lógica más compleja
        // que no interfiera con el HTML de badges, botones, etc.
        // Por ahora solo marcamos visualmente la fila
        fila.style.backgroundColor = '#fffbea';
        setTimeout(() => {
            fila.style.backgroundColor = '';
        }, 2000);
    }
    
    mostrarMensajeResultados(cantidad, termino) {
        // Remover mensaje anterior
        const mensajePrevio = this.tableBody.parentElement.querySelector('.mensaje-busqueda');
        if (mensajePrevio) {
            mensajePrevio.remove();
        }
        
        if (termino === '') {
            return;
        }
        
        // Crear mensaje de resultados
        const mensaje = document.createElement('div');
        mensaje.className = 'alert mensaje-busqueda mt-2';
        
        if (cantidad === 0) {
            mensaje.classList.add('alert-warning');
            mensaje.innerHTML = `
                <i class="fas fa-search"></i> 
                No se encontraron resultados para "<strong>${this.escaparHTML(termino)}</strong>"
                <button type="button" class="btn-close float-end" onclick="this.parentElement.remove()"></button>
            `;
        } else {
            mensaje.classList.add('alert-info');
            mensaje.innerHTML = `
                <i class="fas fa-check-circle"></i> 
                Se encontraron <strong>${cantidad}</strong> resultado(s) para "<strong>${this.escaparHTML(termino)}</strong>"
                <button type="button" class="btn-close float-end" onclick="this.parentElement.remove()"></button>
            `;
        }
        
        this.tableBody.parentElement.parentElement.insertBefore(mensaje, this.tableBody.parentElement);
    }
    
    agregarBotonLimpiar() {
        // Si ya existe, no agregarlo de nuevo
        if (this.inputElement.parentElement.querySelector('.btn-limpiar-busqueda')) {
            return;
        }
        
        const botonLimpiar = document.createElement('button');
        botonLimpiar.type = 'button';
        botonLimpiar.className = 'btn btn-outline-secondary btn-limpiar-busqueda';
        botonLimpiar.innerHTML = '<i class="fas fa-times"></i>';
        botonLimpiar.title = 'Limpiar búsqueda';
        botonLimpiar.style.display = 'none';
        
        botonLimpiar.addEventListener('click', () => {
            this.inputElement.value = '';
            this.buscar('');
            botonLimpiar.style.display = 'none';
        });
        
        this.inputElement.addEventListener('input', () => {
            botonLimpiar.style.display = this.inputElement.value ? 'block' : 'none';
        });
        
        // Insertar el botón después del input
        this.inputElement.parentElement.appendChild(botonLimpiar);
    }
    
    escaparHTML(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    }
}

// Función global para inicializar el buscador
function inicializarBuscadorAvanzado(inputId, tableBodyId, data = null) {
    return new BuscadorAvanzado(inputId, tableBodyId, data);
}
