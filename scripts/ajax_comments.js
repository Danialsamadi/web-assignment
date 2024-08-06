document.addEventListener('DOMContentLoaded', () => {
    const commentForm = document.getElementById('commentForm');

    if (commentForm) {
        commentForm.addEventListener('submit', handleCommentFormSubmit);
    }

    function handleCommentFormSubmit(event) {
        event.preventDefault();

        const content = document.getElementById('commentContent').value.trim();
        const post_id = document.getElementById('postId').value;

        if (content.length < 1) {
            alert('Comment cannot be empty.');
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../server/add_comment.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            console.log(`Add Comment Response: ${this.responseText}`);
            const response = JSON.parse(this.responseText);
            if (response.status === 'success') {
                const newCommentId = response.comment_id;
                const newComment = document.createElement('div');
                newComment.classList.add('comment');
                newComment.id = `comment-${newCommentId}`;
                newComment.innerHTML = `<p>${content}</p><p>by You just now</p><button class='delete-comment-button' data-comment-id='${newCommentId}'>Delete</button>`;
                document.getElementById('commentsList').appendChild(newComment);
                document.getElementById('commentContent').value = '';

                // Adding a slight delay before attaching delete handlers
                setTimeout(() => {
                    attachDeleteHandlers();
                }, 100); // Adjust the delay as necessary
                console.log(`Comment added with ID: ${newCommentId}`);
            } else {
                alert(response.message);
            }
        };

        xhr.send(`post_id=${post_id}&content=${content}`);
    }

    function attachDeleteHandlers() {
        const deleteButtons = document.querySelectorAll('.delete-comment-button');

        deleteButtons.forEach(button => {
            button.removeEventListener('click', deleteComment); // Remove existing event listeners to prevent duplicates
            button.addEventListener('click', deleteComment);
            console.log(`Delete handler attached for comment ID: ${button.getAttribute('data-comment-id')}`);
        });
    }

    function deleteComment(event) {
        const commentId = this.getAttribute('data-comment-id');
        console.log(`Delete button clicked for comment ID: ${commentId}`);

        if (confirm('Are you sure you want to delete this comment?')) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../server/delete_comment.php', true); // Ensure this path is correct
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                console.log(`Delete Comment Response: ${this.responseText}`);
                const response = JSON.parse(this.responseText);
                if (response.status === 'success') {
                    console.log(`Comment deleted: ${commentId}`);
                    const commentElement = document.getElementById(`comment-${commentId}`);
                    if (commentElement) {
                        commentElement.remove();
                        console.log(`Comment element with ID: comment-${commentId} removed from the DOM`);
                    } else {
                        console.log(`Comment element with ID: comment-${commentId} not found in the DOM`);
                    }
                } else {
                    console.log(`Error deleting comment: ${response.message}`);
                    alert(response.message);
                }
            };

            xhr.send(`comment_id=${commentId}`);
        }
    }

    attachDeleteHandlers();
});
