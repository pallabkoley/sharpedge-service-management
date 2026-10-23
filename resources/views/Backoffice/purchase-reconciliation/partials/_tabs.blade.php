<div class="template-toggle">
    <label class="active-label" id="lblRegister">
        <input type="radio" name="reportType" value="register" checked onchange="switchTemplate('register')" />
        <span><i class="bi bi-clipboard-check"></i> Register Purchase</span>
    </label>
    <label id="lblUnregister">
        <input type="radio" name="reportType" value="unregister" onchange="switchTemplate('unregister')" />
        <span><i class="bi bi-clipboard-x"></i> Unregister Purchase</span>
    </label>
</div>

@if(isset($type) && $type === 'unregister')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('#lblRegister input[type="radio"]').checked = false;
            document.querySelector('#lblUnregister input[type="radio"]').checked = true;
            document.getElementById('lblRegister').classList.remove('active-label');
            document.getElementById('lblUnregister').classList.add('active-label');
        });
    </script>
@endif