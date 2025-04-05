<script>
$(document).ready(function() {
    $('#select-all').change(function() {
        $('input[name="selected_products[]"]').prop('checked', $(this).prop('checked'));
    });
    $('input[name="selected_products[]"]').change(function() {
        if (!$(this).prop('checked')) {
            $('#select-all').prop('checked', false);
        } else {
            var allChecked = true;
            $('input[name="selected_products[]"]').each(function() {
                if (!$(this).prop('checked')) {
                    allChecked = false;
                    return false;
                }
            });
            $('#select-all').prop('checked', allChecked);
        }
    });
});
</script>
</div>
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">2374802010243 - Huỳnh Quốc Khôi - Demo Môn Các Nền Tảng Phát Triển Phần Mềm - Powered by Docker &copy; <?= date('Y') ?></p>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>