// Script específico para prevenir duplicación de imágenes en productos

// Variable global para tracking de imágenes por subcategoría
var imageTracker = {
    currentSubcategory: null,
    usedImages: [],
    sessionId: Date.now() + '_' + Math.random().toString(36).substr(2, 9)
};

// Función para limpiar completamente el tracking cuando se abre un modal nuevo
function resetImageTracker() {
    console.log('🧹 Resetting image tracker...');
    imageTracker.currentSubcategory = null;
    imageTracker.usedImages = [];
    imageTracker.sessionId = Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    console.log('✅ Image tracker reset with session ID:', imageTracker.sessionId);
}

// Función para verificar si una imagen ya está siendo usada
function checkImageDuplication(file, subcategoriaId) {
    return new Promise((resolve) => {
        // Verificar en el servidor si la imagen ya existe en la subcategoría
        const formData = new FormData();
        formData.append('action', 'check_image_duplication');
        formData.append('subcategoria_id', subcategoriaId || 0);
        formData.append('image_file', file);
        formData.append('session_id', imageTracker.sessionId);
        
        fetch(base_url + '/Productos/checkImageDuplication', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            resolve(data.isDuplicate || false);
        })
        .catch(error => {
            console.error('Error checking image duplication:', error);
            resolve(false); // En caso de error, permitir la imagen
        });
    });
}

// Función mejorada para agregar imágenes con verificación
async function addImageToGalleryWithCheck(file) {
    const subcategoriaId = $('#listCategoria').val();
    
    console.log('🔍 Checking image for duplication...', {
        filename: file.name,
        subcategoria: subcategoriaId,
        session: imageTracker.sessionId
    });
    
    // Si no hay subcategoría seleccionada, proceder normalmente
    if (!subcategoriaId) {
        console.log('ℹ️ No subcategory selected, proceeding without duplication check');
        addImageToGalleryOriginal(file);
        return;
    }
    
    // Verificar duplicación
    const isDuplicate = await checkImageDuplication(file, subcategoriaId);
    
    if (isDuplicate) {
        swal({
            title: "⚠️ Imagen Duplicada",
            text: "Esta imagen (o muy similar) ya está siendo usada por otro producto en la misma subcategoría. ¿Desea continuar?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, usar imagen",
            cancelButtonText: "Cancelar",
            closeOnConfirm: true
        }, function(isConfirm) {
            if (isConfirm) {
                addImageToGalleryOriginal(file);
            }
        });
    } else {
        addImageToGalleryOriginal(file);
    }
}

// Guardar la función original
if (typeof addImageToGallery === 'function') {
    window.addImageToGalleryOriginal = addImageToGallery;
    
    // Sobrescribir la función original
    window.addImageToGallery = addImageToGalleryWithCheck;
}

// Interceptar el evento de cambio de subcategoría
$(document).ready(function() {
    $('#listCategoria').on('change', function() {
        const newSubcategory = $(this).val();
        if (newSubcategory !== imageTracker.currentSubcategory) {
            console.log('📁 Subcategory changed:', imageTracker.currentSubcategory, '→', newSubcategory);
            imageTracker.currentSubcategory = newSubcategory;
            
            // Solo advertir si hay imágenes seleccionadas Y no es un producto nuevo
            const isNewProduct = !$('#idProducto').val() || $('#idProducto').val() === '';
            
            if (selectedImages && selectedImages.length > 0 && !isNewProduct) {
                swal({
                    title: "⚠️ Cambio de Subcategoría",
                    text: "Ha cambiado la subcategoría y tiene imágenes seleccionadas. ¿Desea mantener las imágenes actuales?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Mantener imágenes",
                    cancelButtonText: "Limpiar imágenes",
                    closeOnConfirm: true
                }, function(isConfirm) {
                    if (!isConfirm) {
                        clearImageGallery();
                    }
                });
            } else if (selectedImages && selectedImages.length > 0 && isNewProduct) {
                // Para productos nuevos, limpiar automáticamente cuando cambie la subcategoría
                console.log('🧹 Auto-clearing images for new product due to subcategory change');
                clearImageGallery();
            }
        }
    });
});

// Interceptar la apertura de modales
const originalOpenModal = window.openModal;
if (typeof originalOpenModal === 'function') {
    window.openModal = function() {
        resetImageTracker();
        return originalOpenModal.apply(this, arguments);
    };
}

console.log('🛡️ Image duplication prevention system loaded');