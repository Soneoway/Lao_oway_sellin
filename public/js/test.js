const imageUploadContainer = document.getElementById('image-upload-container');
const addMoreBtn = document.getElementById('add-more-btn');
const modal = document.getElementById('modal-view');
const modalImage = document.getElementById('modal-view-image');

let imageCount = 1; // Keeps track of the number of upload fields

function previewImage(input) {
  const previewDiv = input.parentElement.querySelector('.preview-image');
  const file = input.files[0];

  if (file) {
    const reader = new FileReader();

    reader.onload = function(e) {
      previewDiv.innerHTML = `<img class="img-polaroid" src="${e.target.result}" alt="Selected Image">`;

      previewDiv.src = e.target.result;
      previewDiv.addEventListener('click', () => {
        modal.style.display = 'block';
        modalImage.src = e.target.result;
      });
    };

    reader.readAsDataURL(file);
  } else {
    previewDiv.innerHTML = '';
  }
}

addMoreBtn.addEventListener('click', function() {
  imageCount++;

  const newUploadDiv = document.createElement('div');
  newUploadDiv.classList.add('image-upload');

  newUploadDiv.innerHTML = `
  <input type="file" class="input-upload" name="images[]" span3" id="image-upload-${imageCount}" accept="image/*" onchange="previewImage(this)" style="
    border-style: solid;
    padding: 2px 2px 2px 2px;
    border-color: lightgray;
    margin-bottom: 5px;
">
  <div class="preview-image"></div>
  <button type="button" class="remove-btn btn btn-danger"><i class="icon icon-trash"></i> Remove</button>
  `;

  imageUploadContainer.appendChild(newUploadDiv);

  const removeBtn = newUploadDiv.querySelector('.remove-btn');
  removeBtn.addEventListener('click', function() {
    imageUploadContainer.removeChild(newUploadDiv);
  });
});


modal.addEventListener('click', (e) => {
  if (e.target === modal) {
    modal.style.display = 'none';
  }
});