<!-- slide_message.php -->
<div id="slideMessage" class="alert text-center d-none"></div>

<style>
    #slideMessage {
        position: fixed;
        top: -100px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        padding: 1rem 2rem;
        width: auto;
        min-width: 300px;
        max-width: 90%;
        transition: top 0.5s ease-in-out;
        border-radius: 6px;
        font-weight: 500;
    }

    #slideMessage.show {
        top: 20px;
    }
</style>


<script>
    function showSlideMessage(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const $messageBox = $('#slideMessage');

        // Reset previous state
        $messageBox
            .removeClass('alert-success alert-danger show')
            .addClass('alert ' + alertClass)
            .html(message)
            .removeClass('d-none'); // Unhide without animation

        // Delay adding 'show' to allow transition
        setTimeout(() => {
            $messageBox.addClass('show'); // Triggers the transition
        }, 10);

        // Remove the 'show' class after 3 seconds
        setTimeout(() => {
            $messageBox.removeClass('show');
        }, 3000);

        // Reapply d-none after slide-out completes (0.5s after hide)
        setTimeout(() => {
            $messageBox.addClass('d-none');
        }, 3500);
    }
</script>

