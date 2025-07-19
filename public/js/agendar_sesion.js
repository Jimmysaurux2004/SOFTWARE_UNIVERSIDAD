/**
 * Validaciones y lógica para agendamiento de sesiones
 * Sistema de Tutorías Universitarias - HU02
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('agendarForm');
    const tutoriaSelect = document.getElementById('tutoria_id');
    const disponibilidadRadios = document.querySelectorAll('input[name="disponibilidad_id"]');
    const tutoriaError = document.getElementById('tutoriaError');
    const disponibilidadError = document.getElementById('disponibilidadError');

    // Validación al enviar formulario
    if (form) {
        form.addEventListener('submit', function(e) {
            const isTutoriaValid = validateTutoria();
            const isDisponibilidadValid = validateDisponibilidad();
            
            if (!isTutoriaValid || !isDisponibilidadValid) {
                e.preventDefault();
            } else {
                // Confirmar antes de enviar
                if (!confirm('¿Está seguro de que desea agendar esta sesión? Esta acción no se puede deshacer.')) {
                    e.preventDefault();
                }
            }
        });
    }
    
    // Validación de tutoría
    if (tutoriaSelect) {
        tutoriaSelect.addEventListener('change', validateTutoria);
    }
    
    // Validación de disponibilidad
    disponibilidadRadios.forEach(radio => {
        radio.addEventListener('change', validateDisponibilidad);
    });
    
    function validateTutoria() {
        if (!tutoriaSelect) return true;
        
        const tutoria = tutoriaSelect.value;
        
        if (!tutoria) {
            showError(tutoriaError, 'Debe seleccionar una tutoría');
            return false;
        }
        
        clearError(tutoriaError);
        return true;
    }
    
    function validateDisponibilidad() {
        const selectedDisponibilidad = document.querySelector('input[name="disponibilidad_id"]:checked');
        
        if (!selectedDisponibilidad) {
            showError(disponibilidadError, 'Debe seleccionar un horario disponible');
            return false;
        }
        
        clearError(disponibilidadError);
        return true;
    }
    
    function showError(element, message) {
        if (element) {
            element.textContent = message;
            element.style.display = 'block';
        }
    }
    
    function clearError(element) {
        if (element) {
            element.textContent = '';
            element.style.display = 'none';
        }
    }
    
    // Actualizar disponibilidad cada 30 segundos (polling)
    if (disponibilidadRadios.length > 0) {
        setInterval(function() {
            // Solo recargar si no hay una opción seleccionada
            const selectedDisponibilidad = document.querySelector('input[name="disponibilidad_id"]:checked');
            if (!selectedDisponibilidad) {
                checkAvailabilityUpdates();
            }
        }, 30000);
    }
    
    function checkAvailabilityUpdates() {
        // Implementar verificación de disponibilidad via AJAX
        // Para la primera iteración, usar recarga simple
        console.log('Verificando actualizaciones de disponibilidad...');
    }
});