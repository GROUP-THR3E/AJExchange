const mainImg = document.querySelector('.view-listing-main-image');
const extraImgs = document.getElementsByClassName('view-listing-extra-image');
for (let i = 0; i < extraImgs.length; i++) {
    extraImgs[i].addEventListener('click', function() {
        mainImg.src = extraImgs[i].src;
    })
}