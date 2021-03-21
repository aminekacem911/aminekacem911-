// $("#new_edit_utilisateur").on('submit', function(){
//     if($("#utilisateur_password").val() != $("#verifpass").val()) {
//         //implémntez votre code
//         alert("Les deux mots de passe saisies sont différents");
//         alert("Merci de renouveler l'opération");
//         return false;
//     }
// })




const items = document.querySelectorAll(".accordion a");
 
function toggleAccordion(){
  this.classList.toggle('active');
  this.nextElementSibling.classList.toggle('active');
}
 
items.forEach(item => item.addEventListener('click', toggleAccordion));