const createImageContainers = document.getElementsByClassName('create-listing-image-container');
for (let i = 0; i < createImageContainers.length; i++) {
   const button = createImageContainers[i].querySelector('.create-listing-image-button');
   button.onchange = function() {
       const url = URL.createObjectURL(this.files[0]);
       createImageContainers[i].style.backgroundImage = 'url(' + url + ')';
       createImageContainers[i].style.backgroundSize = 'cover';
   }
}

window.addEventListener('load', () => {createEnableDisable(); createDisableAll();});
window.addEventListener('keydown', () => {createEnableDisable();});
window.addEventListener('click', () => {createEnableDisable();});

function createDisableAll(){
    enableDisableInputPrice(true);
    enableDisableCharityConf(true);
    enableDisableCharitySelector(true);
    enableDisableInputExchange(true);
    enableDisableCheckConf(true);
}

function countWords(){
    let numWords = getDescription().value.trim().length;
    if(numWords === 0){
        getDescription().value = null;
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
        getCharityConf().checked = false;
    }
}

function enableDisableCharityConf(x){
    document.getElementById('checkCharity').disabled = x;
    if(x === true){
        getCharitySelector(). value = 'Select Charity';
    }
}

function enableDisableInputPrice(x){
    getInputPrice().disabled = x;
    if(x === true){
        getInputPrice().value = '';
    }
}

function enableDisableInputExchange(x){
    getInputExchange().disabled = x;
    if(x === true){
        getInputExchange().value = '';
    }
}

function enableDisableCheckConf(x){
    getCheckConf().disabled = x;
    if(x === true){
        getCheckConf().checked = false;
    }
}

//Accessors for create

function getInputPrice(){
    return document.getElementById("inputPrice");
}

function getInputExchange(){
    return document.getElementById('inputItem');
}

function getCheckConf(){
    return document.getElementById('checkConf');
}

function getInputTitle(){
    return document.getElementById('inputTitle');
}

function getDescription(){
    return document.getElementById('description');
}
function getCharitySelector(){
    return document.getElementById('charity-select')
}
function getCharityConf(){
    return document.getElementById('checkCharity');
}

function getRadioSell(){
    return document.getElementById('radio_sell');
}
function getRadioExchange(){
    return document.getElementById('radio_exchange');
}
function getRadioGiveaway(){
    return document.getElementById('radio_giveaway');
}

//Disables and enables the correct inputs
function createEnableDisable() {
    let selected;
        if (getRadioSell().checked) {
            enableDisableInputPrice(false);
            enableDisableCharityConf(false);
            selected = 'sell';
            if(getCharityConf().checked){
                enableDisableCharitySelector(false);
            }
            else{
                enableDisableCharitySelector(true);
            }
        }
        else if (getRadioExchange().checked) {
            enableDisableInputExchange(false);
            selected = 'exchange';
        }
        else if (getRadioGiveaway().checked) {
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
    if (getInputTitle().value !== '' && getDescription().value !== '') {
        if (selected === 'sell') {
            if (getCharityConf().checked === true) {
                if ((getCharitySelector().value !== '' && getCharitySelector().value !== 'Select Charity') && getInputPrice().value !== '') {

                    enableDisableButton(false);
                }
                else{
                    enableDisableButton(true);
                }
            }
            else if (getInputPrice().value !== '') {
                enableDisableButton(false);
            }
            else{
                enableDisableButton(true);
            }
        }
        else if(selected === 'exchange'){
            if(getInputExchange().value !== ''){
                enableDisableButton(false);
            }
            else{
                enableDisableButton(true);
            }
        }
        else if(selected === 'giveaway'){
            if(getCheckConf().checked === true){
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
