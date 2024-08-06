document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed');
    
    // Register form validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        console.log('Register form found');
        
        registerForm.addEventListener('submit', (event) => {
            console.log('Form submission detected');
            
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            let errorMessage = '';

            console.log(`Username: ${username}`);
            console.log(`Email: ${email}`);
            console.log(`Password: ${password}`);

            if (username.length < 3) {
                errorMessage += 'Username must be at least 3 characters long.\n';
            }

            const emailPattern = /^[^@]+@[^@]+\.[^@]+$/;
            if (!emailPattern.test(email)) {
                errorMessage += 'Invalid email format.\n';
            }

            const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
            if (!passwordPattern.test(password)) {
                errorMessage += 'Password must be at least 6 characters long, contain at least one letter, one number, and one special character.\n';
            }

            if (errorMessage !== '') {
                console.log('Validation errors found');
                alert(errorMessage);
                event.preventDefault();
            } else {
                console.log('Form is valid');
            }
        });
    } else {
        console.log('Register form not found');
    }

    // Comment form validation and AJAX submission

    // Dynamic post filtering
    const searchInput = document.getElementById('search');
    const categoryInput = document.getElementById('category');
    const authorInput = document.getElementById('author');

    if (searchInput) {
        searchInput.addEventListener('keyup', filterPosts);
    }
    if (categoryInput) {
        categoryInput.addEventListener('change', filterPosts);
    }
    if (authorInput) {
        authorInput.addEventListener('change', filterPosts);
    }

    function filterPosts() {
        const searchText = searchInput.value.trim().toLowerCase();
        const selectedCategory = categoryInput.value;
        const selectedAuthor = authorInput.value;
        const posts = document.querySelectorAll('.post');
        let hasResults = false;

        posts.forEach(post => {
            const title = post.querySelector('.post-title').textContent.toLowerCase();
            const author = post.querySelector('.post-author').textContent.toLowerCase();
            const category = post.dataset.category;

            if (title.includes(searchText) && 
                (selectedCategory === '' || category === selectedCategory) && 
                (selectedAuthor === '' || author === selectedAuthor)) {
                post.style.display = '';
                hasResults = true;
            } else {
                post.style.display = 'none';
            }
        });

        const noResultsMessage = document.getElementById('no-results-message');
        if (!hasResults) {
            if (!noResultsMessage) {
                const message = document.createElement('p');
                message.id = 'no-results-message';
                message.textContent = 'No results found.';
                document.getElementById('posts').appendChild(message);
            }
        } else {
            if (noResultsMessage) {
                noResultsMessage.remove();
            }
        }
    }
});
