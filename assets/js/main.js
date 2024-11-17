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


