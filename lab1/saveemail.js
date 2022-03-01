const fs = require('fs');


function getInputValue(){
    // Selecting the input element and get its value 
    var email = document.getElementById("myEmail").value;
    var word = document.getElementById("myWord").value;
    
    // Displaying the value
    alert(email, word);
    
    fs.writeFileSync("/email.txt",email + '\n' + word)
    
}

//module.exports = {getInputValue}