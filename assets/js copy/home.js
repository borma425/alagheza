
const tabButtons = document.querySelectorAll('.tab-button');
const productLinks = document.querySelectorAll('.product-link');

function handleTabClick(event) {
  // Remove active class from all buttons and add to clicked one
  tabButtons.forEach(btn => btn.classList.remove('active'));
  event.currentTarget.classList.add('active');

  const selectedCategory = event.currentTarget.dataset.category;

  // Show/hide products based on category
  productLinks.forEach(product => {
    if (product.dataset.category === selectedCategory) {
      product.style.display = 'block';
    } else {
      product.style.display = 'none';
    }
  });
}

// Add click event to each tab button
tabButtons.forEach(button => {
  button.addEventListener('click', handleTabClick);
});

// Show initial category (first tab)
if (tabButtons.length > 0) {
  tabButtons[0].click();
}




// Track clicked button IDs
let clickedButtons = [];

// Select all the buttons with the class 'compare-button'
const buttons = document.querySelectorAll('.compare-button');

// Add event listeners to each button
buttons.forEach(button => {
 button.addEventListener('click', function(event) {

		   event.stopPropagation();
	 event.preventDefault();
	// Store the ID of the clicked button
	clickedButtons.push(button.id);

				// Update button style to show selected state
		 button.classList.add('selected');
		 // Change icon to checkmark
		 button.innerHTML = `
			 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
				 <path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/>
			 </svg>
		 `;


	// If two buttons are clicked, trigger the alert after 2 seconds
	if (clickedButtons.length === 2) {
	  setTimeout(() => {
		// Show the IDs of the two clicked buttons
		var baseUrl = window.location.origin + window.location.pathname;
		// Redirect to the compare page with the query parameters
		window.location.href = baseUrl + "/compare/?items=[" + clickedButtons[0] + "," + clickedButtons[1] + "]";

	  }, 2000);


	}
  });
});



  // accordion functionality
  document.querySelectorAll('.accordion-header').forEach(header => {
    header.addEventListener('click', () => {
      const item = header.parentElement;
      const isActive = item.classList.contains('active');

      // Close all accordion items
      document.querySelectorAll('.accordion-item').forEach(accItem => {
        accItem.classList.remove('active');
        accItem.querySelector('.accordion-icon').textContent = '+';
      });

      if (!isActive) {
        item.classList.add('active');
        header.querySelector('.accordion-icon').textContent = '-';
      }
    });
  });