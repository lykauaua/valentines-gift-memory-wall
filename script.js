const btn = document.getElementById('loveBtn');
const container = document.querySelector('.container');

// Check if we need to play the "Slide In" animation 
// (for nextPage.html and calendar-pick.html)
if (document.body.classList.contains('page-next') || document.body.classList.contains('page-calendar')) {
    if(container) {
        container.classList.add('slide-in-right');
    }
}

if (btn) {
    btn.addEventListener('click', function(e) {
        // 1. Prevent immediate default action (if it was a link)
        e.preventDefault();

        // 2. Add the slide-out class to the container
        if(container) {
            container.classList.add('slide-out-left');
        }

        // 3. Wait for animation to finish (800ms match css) then go to next page
        setTimeout(() => {
            // Check button text (converted to lowercase to be safe)
            const btnText = btn.innerText.toLowerCase();

            if (btnText.includes("love me")) {
                // Step 1: Welcome Page -> Next Page
                window.location.href = "nextPage.html";
            } else if (btnText.includes("Slide") || btnText.includes("surprise")) {
                // Step 2: Next Page -> Calendar Pick
                window.location.href = "calendar-pick.html";
            } else {
                // Fallback (e.g., from Calendar to Wall if using a button there)
                // Note: The calendar usually uses onclick events on the divs, not this button.
                window.location.href = "photo-wall.html";
            }
        }, 800); 
    });
}