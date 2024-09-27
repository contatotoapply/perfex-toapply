<style>
#tenant-switch-list {
    max-height: 80vh;
    overflow-y: auto;
}
</style>
<script>
document.querySelector('input[name="tenant-switch-list-filter"]').addEventListener('input', function() {
    const filter = this.value.toUpperCase();
    const items = document.querySelectorAll('#tenant-switch-list li:not(:first-child)');
    items.forEach(function(item) {
        const link = item.querySelector('a');
        if (filter === '') {
            item.style.display = ''; // Show all items if the filter is empty
        } else if (link.textContent.toUpperCase().indexOf(filter) > -1) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>