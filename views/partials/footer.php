<!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript Customizado -->
    <script src="<?= BASE_URL ?>/public/assets/js/main.js"></script>
    
    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showFlashMessage('<?= addslashes($_SESSION['flash_success']) ?>', 'success');
            });
        </script>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash_error'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showFlashMessage('<?= addslashes($_SESSION['flash_error']) ?>', 'error');
            });
        </script>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash_info'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showFlashMessage('<?= addslashes($_SESSION['flash_info']) ?>', 'info');
            });
        </script>
        <?php unset($_SESSION['flash_info']); ?>
    <?php endif; ?>
    
    <script>
        // Sistema de Flash Messages
        function showFlashMessage(message, type = 'info') {
            const colors = {
                success: { bg: '#22c55e', border: '#16a34a' },
                error: { bg: '#ef4444', border: '#dc2626' },
                info: { bg: '#3b82f6', border: '#2563eb' }
            };
            
            const icons = {
                success: '✓',
                error: '✕',
                info: 'ℹ'
            };
            
            const color = colors[type] || colors.info;
            const icon = icons[type] || icons.info;
            
            const flashDiv = document.createElement('div');
            flashDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
                padding: 16px 20px;
                background: linear-gradient(145deg, #1e242e, #181d26);
                border-left: 4px solid ${color.border};
                border-radius: 12px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.6);
                display: flex;
                align-items: center;
                gap: 12px;
                animation: slideIn 0.3s ease-out;
            `;
            
            flashDiv.innerHTML = `
                <div style="
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    background: ${color.bg};
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    font-size: 18px;
                    color: white;
                ">${icon}</div>
                <div style="color: #ffffff; flex: 1;">${message}</div>
                <button onclick="this.parentElement.remove()" style="
                    background: none;
                    border: none;
                    color: #8b93a7;
                    font-size: 20px;
                    cursor: pointer;
                    padding: 0;
                    width: 24px;
                    height: 24px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">×</button>
            `;
            
            document.body.appendChild(flashDiv);
            
            // Auto-remover após 5 segundos
            setTimeout(() => {
                flashDiv.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => flashDiv.remove(), 300);
            }, 5000);
        }
        
        // Animações
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(400px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>