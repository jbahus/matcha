function enableAge(){
    var operator = document.getElementById('age_operator');
    var age = document.getElementById('age');
    if (operator.value == '<=>'){
        age.removeAttribute('disabled');
        age.className = "stayWhite interval";
    }
    else{
        age.setAttribute('disabled', false);
        age.className = "interval";
    }
};

function enableScore(){
    var operator = document.getElementById('score_operator');
    var age = document.getElementById('score');
    if (operator.value == '<=>'){
        age.removeAttribute('disabled');
        age.className = "stayWhite interval";
    }
    else{
        age.setAttribute('disabled', false);
        age.className = "interval";
        age.value = "";
    }
};