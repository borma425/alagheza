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
