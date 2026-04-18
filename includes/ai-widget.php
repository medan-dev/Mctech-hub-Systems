<?php
// Futuristic AI Chat Assistant Widget
?>
<!-- AI Chat Assistant Widget (Futuristic) -->
<div id="ai-chat-widget">
    <!-- Chat Button -->
    <button id="ai-chat-toggle" class="ai-chat-toggle glowing-effect" aria-label="Open AI Assistant">
        <div class="bot-icon">
            <svg viewBox="0 0 24 24" fill="none" class="pulse-icon" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v1a7 7 0 0 1-7 7H9a7 7 0 0 1-7-7v-1H1a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/>
                <path d="M8 12v3"/>
                <path d="M16 12v3"/>
                <path d="M9 19h6"/>
            </svg>
        </div>
    </button>

    <!-- Chat Window -->
    <div id="ai-chat-window" class="ai-chat-window glassmorphism-dark">
        <div class="ai-chat-header">
            <div class="header-info">
                <div class="status-pulse"></div>
                <div>
                    <h4 class="ai-name">N.E.O. Agent</h4>
                    <span class="ai-status">Neural Engine Online</span>
                </div>
            </div>
            <button id="ai-chat-close" class="close-btn" aria-label="Close Chat">&times;</button>
        </div>
        
        <div id="ai-chat-messages" class="ai-chat-messages dark-scroll">
            <div class="message ai-message">
                <div class="avatar glow"><i class="fas fa-microchip"></i></div>
                <div class="message-content futuristic-bubble">
                    <strong style="color: #00f2fe;font-size: 0.8rem;text-transform:uppercase;letter-spacing:1px;display:block;margin-bottom:5px;">System Initiated</strong>
                    Hello. I am N.E.O., your advanced Mctech-hub Systems AI interface. How can I assist you with full customer care and digital solutions today?
                </div>
            </div>
        </div>
        
        <div class="ai-chat-input-area border-glow">
            <input type="text" id="ai-chat-input" placeholder="Enter query..." autocomplete="off">
            <button id="ai-chat-send" class="neon-btn"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>
