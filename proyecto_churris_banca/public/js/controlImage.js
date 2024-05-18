const addImageInput = document.getElementById('addImage');

addImageInput.addEventListener('change', function() {
  const file = this.files[0];
  const allowedExtensions = ['jpeg', 'jpg', 'png'];
  const extension = file.name.split('.').pop().toLowerCase();
  const fileSize = file.size;

  if (!allowedExtensions.includes(extension)) {
    alert('Solo se permiten imágenes con extensiones: ' + allowedExtensions.join(', '));
    addImageInput.value = ''; // Borrar la selección del archivo
    return;
  }

  if (fileSize > 4 * 1024 * 1024) {
    alert('El tamaño máximo de la imagen es de 4 MB');
    addImageInput.value = ''; // Borrar la selección del archivo
    return;
  }
});
