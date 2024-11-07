const tabButtons = document.querySelectorAll('.tab-button');

function handleTabClick(event) {
	const clickedTab = event.currentTarget;
	const category = clickedTab.dataset.category;

	tabButtons.forEach(btn => btn.classList.remove('active'));
	clickedTab.classList.add('active');
}

tabButtons.forEach(button => {
	button.addEventListener('click', handleTabClick);
});

// Initialize with the first category or 'all' if no categories are defined
const initialCategory =
	tabButtons.length > 0 ? tabButtons[0].dataset.category : 'all';

// todo: compare functionality
document.addEventListener('click', event => {
	if (event.target.closest('.compare-button')) {
		const productCard = event.target.closest('.product-card');
		const productTitle =
			productCard.querySelector('.product-title').textContent;
		console.log(`Compare clicked for product: ${productTitle}`);
		// todo: Implement compare logic here
	}
});
