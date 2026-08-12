<?php
// Note: Session handling and POST processing are performed in template.php before headers are sent.
// This file renders the login form interface.

if ($_REQUEST['email'] && $_REQUEST['password']) {

    $url = "https://ihr-mail.sakura.ne.jp/appi_portal/api/owner/login";

    $data = [
        "email" => $_REQUEST['email'],
        "password" => $_REQUEST['password']
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $res = json_decode($response, true);
    
    if ($res['_code'] == 200){
        $_SESSION['owner'] = $res['owner'];
        header("Location: /dashboard"); 
        exit();
    }
}
?>
<style>
    /* Premium visual effects and animations */
    @keyframes pulse-slow {
        0%, 100% { transform: scale(1) translate(0px, 0px); opacity: 0.2; }
        33% { transform: scale(1.08) translate(10px, -15px); opacity: 0.25; }
        66% { transform: scale(0.95) translate(-10px, 15px); opacity: 0.18; }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
        20%, 40%, 60%, 80% { transform: translateX(4px); }
    }

    .animate-pulse-slow-1 {
        animation: pulse-slow 8s infinite ease-in-out;
    }
    
    .animate-pulse-slow-2 {
        animation: pulse-slow 12s infinite ease-in-out 2s;
    }
    
    .animate-shake {
        animation: shake 0.4s ease-in-out;
    }

    /* Input autofill custom styling to match dark theme */
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px rgba(15, 23, 42, 0.95) inset !important;
        -webkit-text-fill-color: #fff !important;
        transition: background-color 5000s ease-in-out 0s;
    }
</style>

<div class="relative min-h-[100vh] flex items-center justify-center bg-slate-950 overflow-hidden font-sans select-none px-4 py-12">
    <!-- Ambient Animated Background Blobs -->
    <div class="absolute top-1/4 left-1/4 w-[350px] h-[350px] bg-blue-600 rounded-full blur-[100px] opacity-20 pointer-events-none animate-pulse-slow-1"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[400px] h-[400px] bg-purple-600 rounded-full blur-[120px] opacity-20 pointer-events-none animate-pulse-slow-2"></div>
    
    <!-- Login Card Container -->
    <div class="w-full max-w-md bg-slate-900/60 backdrop-blur-lg border border-white/10 rounded-2xl shadow-2xl p-8 z-10 hover:border-white/15 transition-all duration-300">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center p-3 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl shadow-lg mb-4 text-white">
                <span class="material-icons text-[32px]">apartment</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-blue-400 via-indigo-300 to-purple-400 bg-clip-text text-transparent">
                Owner Portal
            </h1>
            <p class="text-gray-400 text-sm mt-2">オーナー専用ポータルへログイン</p>
        </div>
        
        <!-- Error Message Alert -->
        <?php if (!empty($error)): ?>
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm rounded-xl flex items-start gap-3 animate-shake">
                <span class="material-icons text-rose-400 mt-0.5 text-[18px]">error_outline</span>
                <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Form -->
        <form method="POST" action="#" class="space-y-5">
            <!-- Email Input -->
            <div class="space-y-1.5 group relative">
                <label for="email" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider transition-colors group-focus-within:text-blue-400">
                    メールアドレス
                </label>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-blue-400 transition-colors text-[20px]">
                        mail_outline
                    </span>
                    <input name="email" required placeholder="yamada.tarou@appi.co.jp"
                        class="w-full pl-10 pr-4 py-3 bg-slate-950/80 border border-white/10 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all duration-200">
                </div>
            </div>
            
            <!-- Password Input -->
            <div class="space-y-1.5 group relative">
                <div class="flex justify-between items-center">
                    <label for="password" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider transition-colors group-focus-within:text-blue-400">
                        パスワード
                    </label>
                </div>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-blue-400 transition-colors text-[20px]">
                        lock_outline
                    </span>
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                        class="w-full pl-10 pr-10 py-3 bg-slate-950/80 border border-white/10 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all duration-200">
                    <!-- Eye toggle button -->
                    <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors focus:outline-none cursor-pointer">
                        <span class="material-icons text-[20px]">visibility</span>
                    </button>
                </div>
            </div>
            
            <!-- Extra Options -->
            <div class="flex items-center justify-between text-xs pt-1">
                <a href="#" class="text-blue-400 hover:text-blue-300 font-medium transition-colors">パスワードをお忘れですか？</a>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" id="submit-btn"
                class="w-full mt-2 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold rounded-xl shadow-[0_4px_20px_rgba(37,99,235,0.35)] hover:shadow-[0_6px_25px_rgba(37,99,235,0.45)] transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 active:scale-98 cursor-pointer flex items-center justify-center gap-2">
                <span>ログイン</span>
                <span class="material-icons text-[18px]">login</span>
            </button>
        </form>
        
        
    </div>
</div>

<script>
$(document).ready(function() {

    // 1. Password Visibility Toggle
    $('#toggle-password').click(function() {
        var $pwdInput = $('#password');
        var $icon = $(this).find('.material-icons');
        
        if ($pwdInput.attr('type') === 'password') {
            $pwdInput.attr('type', 'text');
            $icon.text('visibility_off');
        } else {
            $pwdInput.attr('type', 'password');
            $icon.text('visibility');
        }
    });
});
</script>
