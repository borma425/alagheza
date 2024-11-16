function queryParamExistUrl(param = '') {
    param_value = new URLSearchParams(window.location.search).get(param);
    if (param_value != null){
     return param_value
    }else{
     return false
    }
}


// Header
document.addEventListener('DOMContentLoaded', () => {
	const header = document.querySelector('.header');
	const navToggle = document.querySelector('.nav-toggle');
	const navSidebar = document.querySelector('.nav-sidebar');
	const dropdownItems = document.querySelectorAll('.has-dropdown');
	const navLinks = document.querySelectorAll('.nav-link');

	window.addEventListener('scroll', () => {
		if (window.scrollY > 50) {
			header.classList.add('sticky');
		} else {
			header.classList.remove('sticky');
		}
	});

	navToggle.addEventListener('click', () => {
		navSidebar.classList.toggle('active');
		navToggle.classList.toggle('active');
	});

	dropdownItems.forEach(item => {
		const dropdownToggle = item.querySelector('a');
		const dropdown = item.querySelector('.dropdown');

		dropdownToggle.addEventListener('click', e => {
			if (window.innerWidth <= 768) {
				e.preventDefault();
				item.classList.toggle('active');
				dropdown.style.display = item.classList.contains('active')
					? 'block'
					: 'none';
			}
		});

		// Add event listeners to dropdown links
		const dropdownLinks = dropdown.querySelectorAll('a');
		dropdownLinks.forEach(link => {
			link.addEventListener('click', e => {
				e.stopPropagation(); // Prevent the click from bubbling up to the parent
				// Remove the active class from all links
				navLinks.forEach(link => {
					link.classList.remove('active');
				});

				// Close the mobile sidebar when a sub-item is clicked
				if (window.innerWidth <= 768) {
					navSidebar.classList.remove('active');
					navToggle.classList.remove('active');
				}
			});
		});
	});

	window.addEventListener('resize', () => {
		if (window.innerWidth > 768) {
			navSidebar.classList.remove('active');
			navToggle.classList.remove('active');
			dropdownItems.forEach(item => {
				item.classList.remove('active');
				item.querySelector('.dropdown').style.display = '';
			});
		}
	});

	// Close dropdown when clicking outside
	document.addEventListener('click', e => {
		if (!e.target.closest('.has-dropdown')) {
			dropdownItems.forEach(item => {
				item.classList.remove('active');
				if (window.innerWidth <= 768) {
					item.querySelector('.dropdown').style.display = 'none';
				}
			});
		}
	});
});

document.addEventListener('DOMContentLoaded', function () {
	const searchToggle = document.querySelector('.search-toggle');
	const searchWindow = document.getElementById('searchWindow');
	const closeSearch = document.querySelector('.close-search');

	searchToggle.addEventListener('click', function () {
		searchWindow.classList.add('active');
	});

	closeSearch.addEventListener('click', function () {
		searchWindow.classList.remove('active');
	});

	// Close search window when clicking outside of it
	searchWindow.addEventListener('click', function (e) {
		if (e.target === searchWindow) {
			searchWindow.classList.remove('active');
		}
	});
});

document.querySelectorAll('.rating-value').forEach(el => {
	const rating = parseFloat(el.dataset.rating);
	const hue = rating * 12; // This will give a range from 0 (red) to 120 (green)
	el.style.backgroundColor = `hsl(${hue}, 100%, 40%)`;
});

class Pagination {
	constructor(element) {
		this.pagination = element;
		this.pageItems = this.pagination.querySelectorAll(
			'li:not(:first-child):not(:last-child)',
		);
		this.prevButton = this.pagination.querySelector('li:first-child');
		this.nextButton = this.pagination.querySelector('li:last-child');
		this.currentPage = 1;

		this.init();
	}

	init() {
		this.updatePagination();
		this.addEventListeners();
	}

	updatePagination() {
		this.pageItems.forEach((item, index) => {
			item.classList.toggle('active', index + 1 === this.currentPage);
		});

		this.prevButton.classList.toggle('disabled', this.currentPage === 1);
		this.nextButton.classList.toggle(
			'disabled',
			this.currentPage === this.pageItems.length,
		);

		// Dispatch a custom event when page changes
		const event = new CustomEvent('pageChange', {
			detail: {
				currentPage: this.currentPage,
				totalPages: this.pageItems.length,
			},
		});
		this.pagination.dispatchEvent(event);
	}

	addEventListeners() {
		this.pagination.addEventListener('click', e => {
			if (e.target.tagName === 'A') {
				if (
					e.target.getAttribute('aria-label') === 'Previous' &&
					this.currentPage > 1
				) {
					this.currentPage--;
				} else if (
					e.target.getAttribute('aria-label') === 'Next' &&
					this.currentPage < this.pageItems.length
				) {
					this.currentPage++;
				} else if (!isNaN(e.target.textContent)) {
					this.currentPage = parseInt(e.target.textContent);
				}
				this.updatePagination();
			}
		});
	}
}

// Initialize all pagination components when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
	// Find all pagination elements
	const paginationElements = document.querySelectorAll('.pagination');

	// Initialize each pagination component
	const paginationInstances = Array.from(paginationElements).map(
		element => new Pagination(element),
	);
});

// Store selected products for comparison
let selectedProducts = [];
const MAX_PRODUCTS = 2;

// Function to handle compare button click
function handleCompareClick(event) {
	event.preventDefault();
	event.stopPropagation();

	const productCard = event.target.closest('.product-card');
	const productLink = productCard.closest('.product-link');
	const productId = productLink
		.getAttribute('href')
		.split('/')
		.pop()
		.split('.')[0];
	const productTitle = productCard
		.querySelector('.product-title')
		.textContent.trim();
	const compareButton = productCard.querySelector('.compare-button');

	// Check if product is already selected
	const productIndex = selectedProducts.findIndex(p => p.id === productId);

	if (productIndex === -1) {
		// Product not selected - add it if we haven't reached max
		if (selectedProducts.length < MAX_PRODUCTS) {
			selectedProducts.push({
				id: productId,
				title: productTitle,
			});

			// Update button style to show selected state
			compareButton.classList.add('selected');
			// Change icon to checkmark
			compareButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                    <path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/>
                </svg>
            `;

			// If we now have 2 products, redirect to compare page
			if (selectedProducts.length === MAX_PRODUCTS) {
				const queryString = selectedProducts
					.map(p => `product${selectedProducts.indexOf(p) + 1}=${p.id}`)
					.join('&');
				window.location.href = `/compare.html?${queryString}`;
			}
		}
	} else {
		// Product already selected - remove it
		selectedProducts.splice(productIndex, 1);

		// Reset button style
		compareButton.classList.remove('selected');
		// Restore original compare icon
		compareButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="compare-icon">
                <path d="M438.6 150.6c12.5-12.5 12.5-32.8 0-45.3l-96-96c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.7 96 32 96C14.3 96 0 110.3 0 128s14.3 32 32 32l306.7 0-41.4 41.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l96-96zm-333.3 352c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 416 416 416c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0 41.4-41.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-96 96c-12.5 12.5-12.5 32.8 0 45.3l96 96z" />
            </svg>
        `;
	}
}

// Add click event listeners to all compare buttons
document.addEventListener('DOMContentLoaded', () => {
	const compareButtons = document.querySelectorAll('.compare-button');
	compareButtons.forEach(button => {
		button.addEventListener('click', handleCompareClick);
	});
});

// Add some CSS for the selected state
const style = document.createElement('style');
style.textContent = `
    .compare-button.selected {
        background-color: #4CAF50;
        color: white;
    }
    .compare-button.selected svg {
        fill: white;
    }
`;
document.head.appendChild(style);