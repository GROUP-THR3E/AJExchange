const createImageContainers = document.getElementsByClassName('create-listing-image-container');
for (let i = 0; i < createImageContainers.length; i++) {
   const button = createImageContainers[i].querySelector('.create-listing-image-button');
   button.onchange = function() {
       const url = URL.createObjectURL(this.files[0]);
       createImageContainers[i].style.backgroundImage = 'url(' + url + ')';
       createImageContainers[i].style.backgroundSize = 'cover';
   }
}

//create inputs

const inputPrice = document.getElementById("inputPrice");
const inputExchange = document.getElementById('inputItem');
const checkConf = document.getElementById('checkConf');
const inputTitle = document.getElementById('inputTitle');
const descriptionArea = document.getElementById('description');
const charitySelector = document.getElementById('charity-select')
const charityConf = document.getElementById('checkCharity');
const radioSell = document.getElementById('radio_sell');
const radioExchange = document.getElementById('radio_exchange');
const radioGiveaway = document.getElementById('radio_giveaway');
const createImage = document.getElementById('createImage');

document.getElementById('form_create').addEventListener('keydown', () => {createEnableDisable();});
document.getElementById('form_create').addEventListener('click', () => {createEnableDisable();});
window.addEventListener('load', () => {disableAll()});

function disableAll(){
    enableDisableCheckConf(true);
    enableDisableInputPrice(true);
    enableDisableInputExchange(true);
    enableDisableCharitySelector(true);
    enableDisableCharityConf(true);
}


function countWords(){
    let numWords = descriptionArea.value.trim().length;
    if(numWords === 0){
        descriptionArea.value = null;
    }
    document.getElementById("word_count")
        .innerHTML = numWords + '/200';
}


//Toggles for create form
function enableDisableButton(x){
    document.getElementById('create_submit').disabled = x;

}

function enableDisableCharitySelector(x){
    document.getElementById('charity-select').disabled = x;
    if(x === true){
        charityConf.checked = false;
    }
}

function enableDisableCharityConf(x){
    document.getElementById('checkCharity').disabled = x;
    if(x === true){
        charitySelector. value = 'Select Charity';
    }
}

function enableDisableInputPrice(x){
    inputPrice.disabled = x;
    if(x === true){
        inputPrice.value = '';
    }
}

function enableDisableInputExchange(x){
    inputExchange.disabled = x;
    if(x === true){
        inputExchange.value = '';
    }
}

function enableDisableCheckConf(x){
    checkConf.disabled = x;
    if(x === true){
        checkConf.checked = false;
    }
}

//Disables and enables the correct inputs
function createEnableDisable() {
    let selected;
        if (radioSell.checked) {
            enableDisableInputPrice(false);
            enableDisableCharityConf(false);
            selected = 'sell';
            if(charityConf.checked){
                enableDisableCharitySelector(false);
            }
            else{
                enableDisableCharitySelector(true);
            }
        }
        else if (radioExchange.checked) {
            enableDisableInputExchange(false);
            selected = 'exchange';
        }
        else if (radioGiveaway.checked) {
            enableDisableCheckConf(false);
            selected = 'giveaway';
        }
    createDisable(selected);
}
//disables the correct inputs based on selections including submit
function createDisable(selected) {
    if (selected !== 'sell') {
        enableDisableInputPrice(true);
        enableDisableCharityConf(true);
        enableDisableCharitySelector(true);
    }
    if (selected !== 'exchange') {
        enableDisableInputExchange(true);
    }
    if (selected !== 'giveaway') {
        enableDisableCheckConf(true);
    }

    if (inputTitle.value !== '' && descriptionArea.value !== '' &&  createImage.files.length !== 0) {
        if (selected === 'sell') {
            if (charityConf.checked === true) {
                if ((charitySelector.value !== '' && charitySelector.value !== 'Select Charity')) {

                    enableDisableButton(false);
                }
                else{
                    enableDisableButton(true);
                }
            }
            else if (inputPrice.value !== '') {
                enableDisableButton(false);
            }
            else{
                enableDisableButton(true);
            }
        }
        else if(selected === 'exchange'){
            if(inputExchange.value !== ''){
                enableDisableButton(false);
            }
            else{
                enableDisableButton(true);
            }
        }
        else if(selected === 'giveaway'){
            if(checkConf.checked === true){
                enableDisableButton(false);
            }
            else{
                enableDisableButton(true);
            }
        }
    }
    else{
        enableDisableButton(true);
    }
}
