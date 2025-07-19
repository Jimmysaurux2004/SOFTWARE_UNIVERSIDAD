/**
 * Validaciones para formulario de solicitud de tutoría
 * Sistema de Tutorías Universitarias - HU01
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tutoriaForm');
    const cursoSelect = document.getElementById('curso_id');
    const descripcionTextarea = document.getElementById('descripcion');
    const cursoError = document.getElementById('cursoError');
    const descripcionError = document.getElementById('descripcionError');

    // Validación en tiempo real
    cursoSelect.addEventListener('change', validateCurso);
    descripcionTextarea.addEventListener('blur', validateDescripcion);
    descripcionTextarea.addEventListener('input', updateCharacterCount);
    
    // Validación al enviar formulario
    form.addEventListener('submit', function(e) {
        const isCursoValid = validateCurso();
        const isDescripcionValid = validateDescripcion();
        
        if (!isCursoValid || !isDescripcionValid) {
            e.preventDefault();
        }
    });
    
    function validateCurso() {
        const curso = cursoSelect.value;
        
        if (!curso) {
            showError(cursoError, 'Debe seleccionar un curso');
            return false;
        }
        
        clearError(cursoError);
        return true;
    }
    
    function validateDescripcion() {
        const descripcion = descripcionTextarea.value.trim();
        
        if (!descripcion) {
            showError(descripcionError, 'La descripción es requerida');
            return false;
        }
        
        if (descripcion.length < 20) {
            showError(descripcionError, 'La descripción debe tener al menos 20 caracteres');
            return false;
        }
        
        if (descripcion.length > 500) {
            showError(descripcionError, 'La descripción no puede exceder 500 caracteres');
            return false;
        }
        
        clearError(descripcionError);
        return true;
    }
    
    function updateCharacterCount() {
        const length = descripcionTextarea.value.length;
        const help = descripcionTextarea.parentNode.querySelector('.form-help');
        
        if (help) {
            help.textContent = `${length}/500 caracteres. Mínimo 20 caracteres. Sé específico para recibir mejor ayuda.`;
        }
    }
    
    function showError(element, message) {
        element.textContent = message;
        element.style.display = 'block';
    }
    
    function clearError(element) {
        element.textContent = '';
        element.style.display = 'none';
    }
    
    // Inicializar contador de caracteres
    updateCharacterCount();
});