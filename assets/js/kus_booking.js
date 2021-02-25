document.getElementById("kus_booking_service_title_list").addEventListener('change', (e) => {
    const currentValue = e.target.value;
    document.getElementById('kus_booking_form_header').innerText = 'Book For - ' + currentValue;
});

// handle ajax
document.getElementById("kus_booking_form").addEventListener("submit", (e) => {
   e.preventDefault();

   document.getElementById("kus_booking_btn_submit").innerText = 'Loading...';

   const form = document.getElementById("kus_booking_form");
   const modalContet = document.getElementById("kus_booking_modal_content");

   let formData = new FormData(form);
   const data = {
		book_title: formData.get('kus_booking_service_title'),
		name: formData.get('kus_booking_name'),
		email: formData.get('kus_booking_email'),
		date: formData.get('kus_booking_date'),
      time: formData.get('kus_booking_time'),
      message: formData.get('kus_booking_message'),
   };

   fetch(form.dataset.url + '?action=kus_booking_form_data', {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
   })
   .then(
      response => response.json()
   )
   .then(data => {

      if(data.success)
      {
         while (modalContet.firstChild) {
            modalContet.removeChild(modalContet.firstChild);
         }
         const h4 = document.createElement("h4");
         h4.innerText = 'Thank You.';
         modalContet.appendChild(h4);

         setTimeout(function(){ 
            location.reload();
         }, 2000);
      }
      console.log('Success:', data);
   })
   .catch((error) => {
      console.error('Error:', error);
   });
});

var modal = document.getElementById("kus_booking_form_modal");

document.getElementById("kus_booking_btn_widget").addEventListener("click", (e)=> {
	modal.style.display = "block";
});

document.getElementById("kus_booking_form_modal_close").addEventListener("click", (e)=>{
	modal.style.display = "none";
});

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
   if (event.target == modal) {
      modal.style.display = "none";
   }
}