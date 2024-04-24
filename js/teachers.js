function loadOptions(teacherId) {
    let scheduleSelect = document.getElementById('horario_id');
    let scheduleSelectM = document.getElementById('m_horario_id');
    let scheduleSelectX = document.getElementById('x_horario_id');
    let scheduleSelectJ = document.getElementById('j_horario_id');
    let scheduleSelectV = document.getElementById('v_horario_id');
    scheduleSelect.innerHTML = '';
    scheduleSelectM.innerHTML = '';
    scheduleSelectX.innerHTML = '';
    scheduleSelectJ.innerHTML = '';
    scheduleSelectV.innerHTML = '';
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
// jQuery(document).on('click', 'button[name=add-to-cart]', function(e) {
jQuery(document).on('click', '.add-to-cart', function(e) {
    e.preventDefault(); // Prevent default cart addition
  
    // Check if user is logged in and has an active session (same as before)
    jQuery('#wp-auth-check-wrap').show();
    if (!is_user_logged_in() || !has_active_user_session()) {
      // Display login popup
    } else {
      // User is logged in, proceed with regular cart addition
      jQuery('.add_to_cart_button').click(); // Trigger actual cart addition
    }
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