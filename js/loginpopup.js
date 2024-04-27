console.log('Login popup')

jQuery(document).on('click', 'button[name=add-to-cart]', function(e) {
  e.preventDefault(); // Prevent default form submission

  // Get username and password from form fields
  var username = jQuery('#username').val();
  var password = jQuery('#password').val();

  // Send AJAX request to login endpoint
  jQuery.ajax({
    url: '/wp-json/wp/v2/users/login', // WordPress REST API login endpoint
    method: 'POST',
    data: {
      username: 'ing.betovasquez@gmail.com',
      password: 'secret'
    },
    success: function(response) {
      // Login successful
      if (response.jwt) {
        // Store JWT in local storage or cookie (for persistent login)
        localStorage.setItem('wp_jwt_token', response.jwt);

        // Handle successful login (redirect, display message, etc.)
        console.log('Login successful!');
        // window.location.href = '/'; // Redirect to homepage
      } else {
        // Login failed, display error message
        jQuery('#login-form .error-message').html('Invalid username or password.');
      }
    },
    error: function(error) {
      // AJAX error handling
      console.error('Error logging in:', error);
    }
  });
});