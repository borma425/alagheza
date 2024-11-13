
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
     button.addEventListener('click', () => {
       // Store the ID of the clicked button
       clickedButtons.push(button.id);

       // If two buttons are clicked, trigger the alert after 2 seconds
       if (clickedButtons.length === 2) {
         setTimeout(() => {
           // Show the IDs of the two clicked buttons
           alert(`You clicked: ${clickedButtons[0]} and ${clickedButtons[1]}`);
         }, 2000);


       }
     });
   });