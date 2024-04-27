function loadOptions(teacherId) {
    let scheduleSelect = document.getElementById('horario_id');
    let scheduleSelectM = document.getElementById('m_horario_id');
    let scheduleSelectX = document.getElementById('x_horario_id');
    let scheduleSelectJ = document.getElementById('j_horario_id');
    let scheduleSelectV = document.getElementById('v_horario_id');
    scheduleSelect.innerHTML = '<option>Selecciona</option>';
    scheduleSelectM.innerHTML = '<option>Selecciona</option>';
    scheduleSelectX.innerHTML = '<option>Selecciona</option>';
    scheduleSelectJ.innerHTML = '<option>Selecciona</option>';
    scheduleSelectV.innerHTML = '<option>Selecciona</option>';
    fetch('/wp-json/lavs-filter-options/v1/phrase?teacher_id=' + teacherId)
    .then(response => response.json())
    .then(data => {
        console.log(data);
        for(let i = 0; i < data.data.length; i++) {
            if(data.data[i].day == 'L')
                scheduleSelect.innerHTML = scheduleSelect.innerHTML + `<option value='${data.data[i].schedule_id}'>${data.data[i].schedule}</option>`
            if(data.data[i].day == 'M')
                scheduleSelectM.innerHTML = scheduleSelectM.innerHTML + `<option value='${data.data[i].schedule_id}'>${data.data[i].schedule}</option>`
            if(data.data[i].day == 'X')
                scheduleSelectX.innerHTML = scheduleSelectX.innerHTML + `<option value='${data.data[i].schedule_id}'>${data.data[i].schedule}</option>`
            if(data.data[i].day == 'J')
                scheduleSelectJ.innerHTML = scheduleSelectJ.innerHTML + `<option value='${data.data[i].schedule_id}'>${data.data[i].schedule}</option>`
            if(data.data[i].day == 'V')
                scheduleSelectV.innerHTML = scheduleSelectV.innerHTML + `<option value='${data.data[i].schedule_id}'>${data.data[i].schedule}</option>`
        }
    })
    .catch(error => {
        console.error('Error loading schedules:', error);
    });
}

function changeSchedules(event) {
    loadOptions(event.target.value)
} 

document.addEventListener("DOMContentLoaded", function(event) {
    let teacherSelect = document.getElementById('profesor_id')
    
    teacherSelect.addEventListener("change", changeSchedules);

    if(teacherSelect.value) {
        changeSchedules(teacherSelect.value)
    }
});

// Trigger login popup when adding to cart
jQuery(document).on('click', 'button[name=add-to-cart]', function(e) {
// jQuery(document).on('click', '.add-to-cart', function(e) {
    console.log("Agregar al carrito");
    let days = [];
    let teacherSelect = document.getElementById('profesor_id');
    
    let scheduleSelect = document.getElementById('horario_id');
    let scheduleSelectM = document.getElementById('m_horario_id');
    let scheduleSelectX = document.getElementById('x_horario_id');
    let scheduleSelectJ = document.getElementById('j_horario_id');
    let scheduleSelectV = document.getElementById('v_horario_id');

    console.log(isNaN(scheduleSelect.value))
    if(!isNaN(scheduleSelect.value)) {
      days.push('L');
    }
    if(!isNaN(scheduleSelectM.value)) {
      days.push('M');
    }
    if(!isNaN(scheduleSelectX.value)) {
      days.push('X');
    }
    if(!isNaN(scheduleSelectJ.value)) {
      days.push('J');
    }
    if(!isNaN(scheduleSelectV.value)) {
      days.push('V');
    }

    if(days.length <= 1) {
      alert('Debes seleccionar al menos 2 horarios');
      e.preventDefault(); // Prevent default cart addition
      return;
    }
    if(days.length > 4) {
      alert('Debes seleccionar máximo 4 horarios');
      e.preventDefault(); // Prevent default cart addition
      return;
    }
    console.log(days)
    console.log(teacherSelect.value)
    if(!teacherSelect.value || teacherSelect.value == '' || teacherSelect.value == 'Selecciona') {
      alert('Debes seleccionar al profesor');
      e.preventDefault(); // Prevent default cart addition
      return;
    }
    jQuery(`button[name="add-to-cart"]`).closest(`form`);
});
  
  // Handle login form submission
  jQuery('#login-popup form').submit(function(e) {
    e.preventDefault();
  
    // Get username and password from form fields
    var username = jQuery('#username').val();
    var password = jQuery('#password').val();
  
    // Send AJAX request to login endpoint
    jQuery.ajax({
      url: '/wp-json/wc/v2/customers/login',
      method: 'POST',
      data: {
        username: username,
        password: password
      },
      success: function(response) {
        // Login successful
        if (response.user) {
          // Close popup, refresh cart, and display success message
          jQuery('#login-popup').hide();
          jQuery('.cart').load(window.location.href + ' .cart'); // Refresh cart content
          jQuery('.woocommerce-notices-wrapper').append('<div class="woocommerce-notice notice"><p>You have successfully logged in and the product has been added to your cart.</p></div>');
        } else {
          // Login failed, display error message
          jQuery('#login-popup .error-message').html('Invalid username or password.');
        }
      },
      error: function(error) {
        // AJAX error handling
        console.error('Error logging in:', error);
      }
    });
  });