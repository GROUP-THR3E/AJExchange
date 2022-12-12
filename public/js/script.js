const createImageContainers = document.getElementsByClassName('create-listing-image-container');
for (let i = 0; i < createImageContainers.length; i++) {
   const button = createImageContainers[i].querySelector('.create-listing-image-button');
   button.onchange = function() {
       const url = URL.createObjectURL(this.files[0]);
       createImageContainers[i].style.backgroundImage = 'url(' + url + ')';
       createImageContainers[i].style.backgroundSize = 'cover';
   }
}