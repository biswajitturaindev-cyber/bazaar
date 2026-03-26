<footer
    class="px-4 py-3 bg-slate-800 text-white border-gray-200 fixed bottom-0 right-0 z-10 w-full -ml-6 text-center text-xs"
>
    <span id="datetime"></span> --- Reshera Industries Pvt. Ltd.
</footer>
<script>
function updateDateTime() {
    const now = new Date();

    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    };

    const formatted = now.toLocaleString('en-US', options);

    document.getElementById("datetime").textContent = formatted;
}

updateDateTime();
setInterval(updateDateTime, 60000); // update every minute
</script>