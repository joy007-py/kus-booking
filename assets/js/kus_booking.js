document.getElementById("kus_booking_service_title_list").addEventListener('change', (e) => {
    const currentValue = e.target.value;
    document.getElementById('kus_booking_form_header').innerText = 'Book For - ' + currentValue;
});

// handle ajax
document.getElementById("kus_booking_form").addEventListener("submit", (e) => {

    e.preventDefault();

    const form = document.getElementById("kus_booking_form");

    let formData = new FormData(form);

    const data = {
        book_title: formData.get('kus_booking_service_title'),
        name: formData.get('kus_booking_name'),
        email: formData.get('kus_booking_email'),
        date: formData.get('kus_booking_date')
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
        console.log('Success:', data);
    })
    .catch((error) => {
        console.error('Error:', error);
    });
});