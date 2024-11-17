document.addEventListener('DOMContentLoaded', () => {
	const header = document.querySelector('.header');
	const navToggle = document.querySelector('.nav-toggle');
	const navSidebar = document.querySelector('.nav-sidebar');
	const dropdownItems = document.querySelectorAll('.has-dropdown');
	const navLinks = document.querySelectorAll('.nav-link');
	const mediaQueryThreshold = 768;
  
	// Throttle scroll event for performance
	let scrollTimeout;
	const onScroll = () => {
	  if (window.scrollY > 50) {
		header.classList.add('sticky');
	  } else {
		header.classList.remove('sticky');
	  }
	};
  
	window.addEventListener('scroll', () => {
	  if (!scrollTimeout) {
		scrollTimeout = setTimeout(() => {
		  onScroll();
		  scrollTimeout = null;
		}, 50); // Adjust scroll delay as needed
	  }
	});
  
	// Handle nav toggle for mobile
	const toggleNav = () => {
	  navSidebar.classList.toggle('active');
	  navToggle.classList.toggle('active');
	};
  
	navToggle.addEventListener('click', toggleNav);
  
	// Dropdown toggle for mobile menu
	dropdownItems.forEach(item => {
	  const dropdownToggle = item.querySelector('a');
	  const dropdown = item.querySelector('.dropdown');
  
	  dropdownToggle.addEventListener('click', (e) => {
		if (window.innerWidth <= mediaQueryThreshold) {
		  e.preventDefault();
		  const isActive = item.classList.toggle('active');
		  dropdown.style.display = isActive ? 'block' : 'none';
		}
	  });
  
	  // Close dropdown on link click
	  const dropdownLinks = dropdown.querySelectorAll('a');
	  dropdownLinks.forEach(link => {
		link.addEventListener('click', (e) => {
		  e.stopPropagation();
		  navLinks.forEach(link => link.classList.remove('active'));
		  if (window.innerWidth <= mediaQueryThreshold) {
			navSidebar.classList.remove('active');
			navToggle.classList.remove('active');
		  }
		});
	  });
	});
  
	// Debounce resize event
	let resizeTimeout;
	const onResize = () => {
	  if (window.innerWidth > mediaQueryThreshold) {
		navSidebar.classList.remove('active');
		navToggle.classList.remove('active');
		dropdownItems.forEach(item => {
		  item.classList.remove('active');
		  item.querySelector('.dropdown').style.display = '';
		});
	  }
	};
  
	window.addEventListener('resize', () => {
	  if (!resizeTimeout) {
		resizeTimeout = setTimeout(() => {
		  onResize();
		  resizeTimeout = null;
		}, 200); // Adjust resize delay as needed
	  }
	});
  
	// Close dropdown when clicking outside
	document.addEventListener('click', (e) => {
	  if (!e.target.closest('.has-dropdown')) {
		dropdownItems.forEach(item => {
		  item.classList.remove('active');
		  if (window.innerWidth <= mediaQueryThreshold) {
			item.querySelector('.dropdown').style.display = 'none';
		  }
		});
	  }
	});
  });



  document.addEventListener('DOMContentLoaded', () => {
	const searchToggle = document.querySelector('.search-toggle');
	const searchWindow = document.getElementById('searchWindow');
	const closeSearch = document.querySelector('.close-search');
  
	// Toggle search window visibility
	const toggleSearchWindow = (action) => {
	  searchWindow.classList[action]('active');
	};
  
	searchToggle.addEventListener('click', () => toggleSearchWindow('add'));
	closeSearch.addEventListener('click', () => toggleSearchWindow('remove'));
  
	// Close search window if clicked outside
	searchWindow.addEventListener('click', (e) => {
	  if (e.target === searchWindow) {
		toggleSearchWindow('remove');
	  }
	});
  });



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
