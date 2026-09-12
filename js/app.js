/**
 * MomasPay Interactive Prototype JS
 * Handles screen switching, countdown timers, OTP inputs, password toggles, balance hiding,
 * payment gateway modal, electric company picker modal, AND interactive promo banner slider carousel.
 */

// Automatic MomasPay Preloader Handler
(function initMomasPreloader() {
  const preloaderHTML = `
    <div id="momas-preloader" class="momas-preloader">
      <div class="preloader-content">
        <div class="preloader-logo-wrapper">
          <svg class="preloader-logo-svg" viewBox="0 0 100 65" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="15" y="32" width="14" height="24" rx="7" transform="rotate(-35 15 32)" fill="#00C868"/>
            <rect x="36" y="18" width="14" height="42" rx="7" transform="rotate(-35 36 18)" fill="#00A859"/>
            <rect x="57" y="4" width="14" height="60" rx="7" transform="rotate(-35 57 4)" fill="#007D3E"/>
          </svg>
        </div>
        <div class="preloader-brand-title">MOMAS<span>Pay</span></div>
        <div class="preloader-subtext">Smart Utility & Vending Platform</div>
        <div class="preloader-spinner-ring"></div>
      </div>
    </div>
  `;

  function mountPreloader() {
    if (!document.getElementById('momas-preloader') && document.body) {
      document.body.insertAdjacentHTML('afterbegin', preloaderHTML);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountPreloader);
  } else {
    mountPreloader();
  }

  function hidePreloader() {
    const el = document.getElementById('momas-preloader');
    if (el && !el.classList.contains('fade-out')) {
      el.classList.add('fade-out');
      setTimeout(() => {
        if (el && el.parentNode) el.parentNode.removeChild(el);
      }, 500);
    }
  }

  window.addEventListener('load', () => setTimeout(hidePreloader, 350));
  setTimeout(hidePreloader, 1000); // Safety fallback timeout
})();

document.addEventListener('DOMContentLoaded', () => {
  // Password Visibility Toggle
  const toggleButtons = document.querySelectorAll('.toggle-password');
  toggleButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.previousElementSibling || btn.parentElement.querySelector('input');
      if (input) {
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        const eyeIcon = btn.querySelector('svg');
        if (eyeIcon) {
          eyeIcon.style.opacity = isPassword ? '0.5' : '1';
        }
      }
    });
  });

  // Balance Hide / Show Toggle (Dashboard Main Wallet & Available Units)
  const toggleBalanceBtns = document.querySelectorAll('.toggle-balance-btn');
  toggleBalanceBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const parentRow = btn.closest('.balance-value-row');
      const textSpan = parentRow ? parentRow.querySelector('.balance-text') : null;
      
      if (textSpan) {
        const isHidden = textSpan.getAttribute('data-hidden') === 'true';
        if (!textSpan.hasAttribute('data-original')) {
          textSpan.setAttribute('data-original', textSpan.textContent.trim());
        }
        
        if (isHidden) {
          textSpan.textContent = textSpan.getAttribute('data-original');
          textSpan.setAttribute('data-hidden', 'false');
          btn.innerHTML = `<svg class="eye-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
        } else {
          textSpan.textContent = '••••••••';
          textSpan.setAttribute('data-hidden', 'true');
          btn.innerHTML = `<svg class="eye-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
        }
      }
    });
  });

  // Promo Banner Carousel Slider Logic
  const promoSlider = document.getElementById('promo-slider');
  const promoDots = document.querySelectorAll('#promo-dots .dot');
  let currentSlide = 0;
  let autoSlideTimer = null;

  function updateActiveDot(index) {
    promoDots.forEach((dot, idx) => {
      if (idx === index) {
        dot.classList.add('active');
      } else {
        dot.classList.remove('active');
      }
    });
  }

  function goToSlide(index) {
    if (!promoSlider) return;
    const slides = promoSlider.querySelectorAll('.promo-banner-card');
    if (slides[index]) {
      const slideWidth = promoSlider.offsetWidth;
      promoSlider.scrollTo({
        left: index * slideWidth,
        behavior: 'smooth'
      });
      currentSlide = index;
      updateActiveDot(index);
    }
  }

  promoDots.forEach(dot => {
    dot.addEventListener('click', () => {
      const slideIdx = parseInt(dot.getAttribute('data-slide'), 10);
      goToSlide(slideIdx);
      resetAutoSlide();
    });
  });

  if (promoSlider) {
    promoSlider.addEventListener('scroll', () => {
      const slideWidth = promoSlider.offsetWidth;
      if (slideWidth > 0) {
        const slideIndex = Math.round(promoSlider.scrollLeft / slideWidth);
        if (slideIndex !== currentSlide) {
          currentSlide = slideIndex;
          updateActiveDot(slideIndex);
        }
      }
    });
  }

  function startAutoSlide() {
    autoSlideTimer = setInterval(() => {
      if (!promoSlider) return;
      const totalSlides = promoSlider.querySelectorAll('.promo-banner-card').length;
      const nextSlide = (currentSlide + 1) % totalSlides;
      goToSlide(nextSlide);
    }, 3500);
  }

  function resetAutoSlide() {
    clearInterval(autoSlideTimer);
    startAutoSlide();
  }

  if (promoSlider) {
    startAutoSlide();
  }

  // Electric Company Picker Modal Logic (pay-other-meter.html)
  const openDiscoModalBtn = document.getElementById('open-disco-modal');
  const discoModal = document.getElementById('disco-modal');
  const selectedDiscoInput = document.getElementById('selected-disco-input');
  const discoSearch = document.getElementById('disco-search');
  const discoItems = document.querySelectorAll('.disco-item');

  if (openDiscoModalBtn && discoModal) {
    openDiscoModalBtn.addEventListener('click', () => {
      discoModal.classList.add('active');
    });
  }

  if (discoModal) {
    discoModal.addEventListener('click', (e) => {
      if (e.target === discoModal) {
        discoModal.classList.remove('active');
      }
    });
  }

  discoItems.forEach(item => {
    item.addEventListener('click', () => {
      const selectedDisco = item.getAttribute('data-disco');
      if (selectedDiscoInput && selectedDisco) {
        selectedDiscoInput.value = selectedDisco;
        const gatewayServiceName = document.getElementById('gateway-service-name');
        if (gatewayServiceName) gatewayServiceName.textContent = selectedDisco;
      }
      if (discoModal) {
        discoModal.classList.remove('active');
      }
    });
  });

  if (discoSearch) {
    discoSearch.addEventListener('input', (e) => {
      const query = e.target.value.toLowerCase().trim();
      discoItems.forEach(item => {
        const name = item.querySelector('.disco-name')?.textContent.toLowerCase() || '';
        if (name.includes(query)) {
          item.style.display = 'flex';
        } else {
          item.style.display = 'none';
        }
      });
    });
  }

  // Payment Gateway Modal Bottom Sheet Controls
  const modal = document.getElementById('gateway-modal');
  const otherModal = document.getElementById('other-gateway-modal');
  const openModalBtn = document.getElementById('open-gateway-btn');
  const openOtherModalBtn = document.getElementById('open-other-gateway-btn');
  const openModalNavBtn = document.getElementById('open-gateway-nav-btn');

  function openGatewayModal(targetModal) {
    if (targetModal) {
      targetModal.classList.add('active');
    }
  }

  function closeGatewayModal(targetModal) {
    if (targetModal) {
      targetModal.classList.remove('active');
    }
  }

  if (openModalBtn && modal) {
    openModalBtn.addEventListener('click', () => openGatewayModal(modal));
  }

  if (openOtherModalBtn && otherModal) {
    openOtherModalBtn.addEventListener('click', () => openGatewayModal(otherModal));
  }

  if (openModalNavBtn && modal) {
    openModalNavBtn.addEventListener('click', () => openGatewayModal(modal));
  }

  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeGatewayModal(modal);
    });
  }

  if (otherModal) {
    otherModal.addEventListener('click', (e) => {
      if (e.target === otherModal) closeGatewayModal(otherModal);
    });
  }

  // OTP Digits Auto-focus & Navigation
  const otpInputs = document.querySelectorAll('.otp-box');
  otpInputs.forEach((input, index) => {
    input.addEventListener('keyup', (e) => {
      if (e.key >= '0' && e.key <= '9') {
        input.value = e.key;
        if (index < otpInputs.length - 1) {
          otpInputs[index + 1].focus();
        }
      } else if (e.key === 'Backspace') {
        input.value = '';
        if (index > 0) {
          otpInputs[index - 1].focus();
        }
      }
    });

    input.addEventListener('paste', (e) => {
      e.preventDefault();
      const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
      if (/^\d{4}$/.test(pasteData)) {
        pasteData.split('').forEach((char, i) => {
          if (otpInputs[i]) otpInputs[i].value = char;
        });
        if (otpInputs[otpInputs.length - 1]) otpInputs[otpInputs.length - 1].focus();
      }
    });
  });

  // OTP Resend Countdown Timer (02:05)
  let timerSeconds = 125;
  const timerElement = document.getElementById('otp-timer');

  function updateTimer() {
    if (!timerElement) return;
    const mins = Math.floor(timerSeconds / 60);
    const secs = timerSeconds % 60;
    timerElement.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    
    if (timerSeconds > 0) {
      timerSeconds--;
    } else {
      timerElement.textContent = '00:00';
    }
  }

  if (timerElement) {
    setInterval(updateTimer, 1000);
  }

  // Token Filter Modal Logic (reprint-token.html)
  const openFilterModalBtn = document.getElementById('open-filter-modal');
  const filterModal = document.getElementById('token-filter-modal');
  const closeFilterBtn = document.getElementById('close-filter-btn');
  const filterPills = document.querySelectorAll('.filter-pill');
  const tokenSearchInput = document.getElementById('token-search');
  const tokenCardItems = document.querySelectorAll('.token-card-item');

  if (openFilterModalBtn && filterModal) {
    openFilterModalBtn.addEventListener('click', () => {
      filterModal.classList.add('active');
    });
  }

  if (closeFilterBtn && filterModal) {
    closeFilterBtn.addEventListener('click', () => {
      filterModal.classList.remove('active');
    });
  }

  if (filterModal) {
    filterModal.addEventListener('click', (e) => {
      if (e.target === filterModal) {
        filterModal.classList.remove('active');
      }
    });
  }

  filterPills.forEach(pill => {
    pill.addEventListener('click', () => {
      filterPills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
    });
  });

  if (tokenSearchInput) {
    tokenSearchInput.addEventListener('input', (e) => {
      const query = e.target.value.toLowerCase().trim();
      tokenCardItems.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(query)) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  }

  // Choose Estate Modal Logic (generate-token.html)
  const estateModal = document.getElementById('estate-modal');
  const openEstateModalBtn = document.getElementById('open-estate-modal');
  const changeEstateBtn = document.getElementById('change-estate-btn');
  const closeEstateBtn = document.getElementById('close-estate-btn');
  const selectedEstateInput = document.getElementById('selected-estate-input');
  const estateSearchInput = document.getElementById('estate-search');
  const estateCards = document.querySelectorAll('.estate-card-item');

  function openEstateModal() {
    if (estateModal) estateModal.classList.add('active');
  }

  function closeEstateModal() {
    if (estateModal) estateModal.classList.remove('active');
  }

  if (openEstateModalBtn) openEstateModalBtn.addEventListener('click', openEstateModal);
  if (changeEstateBtn) changeEstateBtn.addEventListener('click', openEstateModal);
  if (closeEstateBtn) closeEstateBtn.addEventListener('click', closeEstateModal);

  if (estateModal) {
    estateModal.addEventListener('click', (e) => {
      if (e.target === estateModal) closeEstateModal();
    });
  }

  estateCards.forEach(card => {
    card.addEventListener('click', () => {
      estateCards.forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      const estateName = card.textContent.trim();
      if (selectedEstateInput && estateName) {
        selectedEstateInput.value = estateName;
      }
      closeEstateModal();
    });
  });

  if (estateSearchInput) {
    estateSearchInput.addEventListener('input', (e) => {
      const query = e.target.value.toLowerCase().trim();
      estateCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(query)) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  }

  // Choose Service Modal Logic (service.html)
  const serviceModal = document.getElementById('service-picker-modal');
  const openServiceModalBtn = document.getElementById('open-service-modal');
  const selectedServiceInput = document.getElementById('selected-service-input');
  const serviceSearchInput = document.getElementById('service-search');
  const serviceOptions = document.querySelectorAll('.service-option-item');
  const searchArtisanBtn = document.getElementById('search-artisan-btn');
  const availableServicesSection = document.getElementById('available-services-section');
  const emptyServiceBox = document.getElementById('empty-service-box');

  function openServiceModal() {
    if (serviceModal) serviceModal.classList.add('active');
  }

  function closeServiceModal() {
    if (serviceModal) serviceModal.classList.remove('active');
  }

  if (openServiceModalBtn) openServiceModalBtn.addEventListener('click', openServiceModal);
  if (selectedServiceInput) selectedServiceInput.addEventListener('click', openServiceModal);

  if (serviceModal) {
    serviceModal.addEventListener('click', (e) => {
      if (e.target === serviceModal) closeServiceModal();
    });
  }

  serviceOptions.forEach(opt => {
    opt.addEventListener('click', () => {
      const serviceName = opt.textContent.trim();
      if (selectedServiceInput && serviceName) {
        selectedServiceInput.value = serviceName;
      }
      closeServiceModal();
    });
  });

  if (serviceSearchInput) {
    serviceSearchInput.addEventListener('input', (e) => {
      const query = e.target.value.toLowerCase().trim();
      serviceOptions.forEach(opt => {
        const text = opt.textContent.toLowerCase();
        if (text.includes(query)) {
          opt.style.display = 'block';
        } else {
          opt.style.display = 'none';
        }
      });
    });
  }

  if (searchArtisanBtn) {
    searchArtisanBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const val = selectedServiceInput ? selectedServiceInput.value.toLowerCase() : '';
      if (val.includes('cleaner') || val.includes('painter') || val.includes('carpenter')) {
        if (availableServicesSection) availableServicesSection.style.display = 'none';
        if (emptyServiceBox) emptyServiceBox.style.display = 'flex';
      } else {
        if (availableServicesSection) availableServicesSection.style.display = 'flex';
        if (emptyServiceBox) emptyServiceBox.style.display = 'none';
      }
    });
  }

  // Network Provider Selector (airtime.html)
  const networkBadges = document.querySelectorAll('.network-badge-box');
  const gatewayAirtimeService = document.getElementById('gateway-airtime-service');

  networkBadges.forEach(badge => {
    badge.addEventListener('click', () => {
      networkBadges.forEach(b => b.classList.remove('selected'));
      badge.classList.add('selected');
      const networkName = badge.getAttribute('data-network') || 'MTN';
      if (gatewayAirtimeService) {
        gatewayAirtimeService.textContent = `${networkName.toUpperCase()} AIRTIME`;
      }
    });
  });

  // Issue Type Modal Logic (raise-ticket.html)
  const issueTypeModal = document.getElementById('issue-type-modal');
  const openIssueTypeModalBtn = document.getElementById('open-issue-type-modal');
  const selectedIssueTypeInput = document.getElementById('selected-issue-type-input');
  const issueTypeOptions = document.querySelectorAll('.issue-type-option');

  function openIssueTypeModal() {
    if (issueTypeModal) issueTypeModal.classList.add('active');
  }

  function closeIssueTypeModal() {
    if (issueTypeModal) issueTypeModal.classList.remove('active');
  }

  if (openIssueTypeModalBtn) openIssueTypeModalBtn.addEventListener('click', openIssueTypeModal);
  if (selectedIssueTypeInput) selectedIssueTypeInput.addEventListener('click', openIssueTypeModal);

  if (issueTypeModal) {
    issueTypeModal.addEventListener('click', (e) => {
      if (e.target === issueTypeModal) closeIssueTypeModal();
    });
  }

  issueTypeOptions.forEach(opt => {
    opt.addEventListener('click', () => {
      const typeText = opt.textContent.trim();
      if (selectedIssueTypeInput && typeText) {
        selectedIssueTypeInput.value = typeText;
      }
      closeIssueTypeModal();
    });
  });

  // Raise Ticket Form Submit & Ticket Received Modal (raise-ticket.html)
  const raiseTicketForm = document.getElementById('raise-ticket-form');
  const ticketSuccessModal = document.getElementById('ticket-success-modal');

  if (raiseTicketForm) {
    raiseTicketForm.addEventListener('submit', (e) => {
      e.preventDefault();
      if (ticketSuccessModal) {
        ticketSuccessModal.classList.add('active');
      }
    });
  }

  // Live Chat Messaging Logic (ticket-chat.html)
  const sendChatMsgBtn = document.getElementById('send-chat-msg-btn');
  const chatMsgInput = document.getElementById('chat-msg-input');
  const chatMessagesContainer = document.getElementById('chat-messages-container');

  function sendChatMessage() {
    if (!chatMsgInput || !chatMessagesContainer) return;
    const msgText = chatMsgInput.value.trim();
    if (!msgText) return;

    // Append User Bubble
    const userBubble = document.createElement('div');
    userBubble.className = 'chat-bubble-user';
    userBubble.innerHTML = `${msgText}<div class="chat-timestamp">now</div>`;
    chatMessagesContainer.appendChild(userBubble);

    chatMsgInput.value = '';
    chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;

    // Simulate Agent Typing & Reply
    setTimeout(() => {
      const typingBubble = document.createElement('div');
      typingBubble.className = 'chat-bubble-agent';
      typingBubble.innerHTML = `<span style="font-weight:700;">•••</span> <span style="font-size:0.75rem; color:#94A3B8;">typing</span>`;
      chatMessagesContainer.appendChild(typingBubble);
      chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;

      setTimeout(() => {
        typingBubble.remove();
        const agentBubble = document.createElement('div');
        agentBubble.className = 'chat-bubble-agent';
        agentBubble.innerHTML = `Thank you for your response. Our technical team is processing your request.<div class="chat-timestamp">now</div>`;
        chatMessagesContainer.appendChild(agentBubble);
        chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;
      }, 2000);
    }, 800);
  }

  if (sendChatMsgBtn) {
    sendChatMsgBtn.addEventListener('click', sendChatMessage);
  }

  if (chatMsgInput) {
    chatMsgInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        sendChatMessage();
      }
    });
  }

  // Copy to Clipboard buttons
  document.querySelectorAll('.copy-badge-icon').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const textToCopy = btn.previousElementSibling?.textContent;
      if (textToCopy) {
        navigator.clipboard.writeText(textToCopy);
        const originalText = btn.textContent;
        btn.textContent = '✓';
        setTimeout(() => {
          btn.textContent = originalText;
        }, 1500);
      }
    });
  });

  // Admin Sidebar Toggle Click Handler (Responsive Mobile & Desktop Collapse)
  document.addEventListener('click', (e) => {
    const toggleBtn = e.target.closest('#sidebar-toggle, .admin-sidebar-toggle');
    const backdrop = e.target.closest('.sidebar-backdrop');
    const layout = document.querySelector('.admin-layout');
    
    if (toggleBtn && layout) {
      e.preventDefault();
      if (window.innerWidth <= 900) {
        layout.classList.toggle('sidebar-mobile-open');
      } else {
        layout.classList.toggle('sidebar-collapsed');
      }
    } else if (backdrop && layout) {
      layout.classList.remove('sidebar-mobile-open');
    }
  });

  // Auto-close mobile sidebar drawer on navigation item click
  document.querySelectorAll('.admin-nav-item').forEach(link => {
    link.addEventListener('click', () => {
      const layout = document.querySelector('.admin-layout');
      if (layout && window.innerWidth <= 900) {
        layout.classList.remove('sidebar-mobile-open');
      }
    });
  });

  // Dynamic Admin Profile Dropdown Menu (Profile, Setting, Logout)
  document.addEventListener('click', (e) => {
    const profilePill = e.target.closest('.admin-profile-pill');

    if (profilePill) {
      e.stopPropagation();
      let wrapper = profilePill.closest('.admin-profile-wrapper');
      
      // Wrap pill if not already wrapped
      if (!wrapper) {
        wrapper = document.createElement('div');
        wrapper.className = 'admin-profile-wrapper';
        profilePill.parentNode.insertBefore(wrapper, profilePill);
        wrapper.appendChild(profilePill);
      }

      let dropdown = wrapper.querySelector('.admin-profile-dropdown-menu');
      if (!dropdown) {
        dropdown = document.createElement('div');
        dropdown.className = 'admin-profile-dropdown-menu';
        dropdown.innerHTML = `
          <div class="dropdown-header">
            <div style="font-weight: 800; font-size: 0.9rem; color: #0F172A;">Admin User</div>
            <div style="font-size: 0.75rem; color: #64748B; font-weight: 600;">admin@momaspay.com</div>
          </div>
          <div class="dropdown-divider"></div>
          <a href="admin-profile.html" class="dropdown-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Profile</span>
          </a>
          <a href="admin-settings.html" class="dropdown-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span>Setting</span>
          </a>
          <div class="dropdown-divider"></div>
          <div class="dropdown-item logout" id="dropdown-logout-action">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Logout</span>
          </div>
        `;
        wrapper.appendChild(dropdown);

        // Bind logout action
        const logoutBtn = dropdown.querySelector('#dropdown-logout-action');
        if (logoutBtn) {
          logoutBtn.addEventListener('click', (evt) => {
            evt.preventDefault();
            dropdown.classList.remove('active');
            profilePill.classList.remove('active');
            if (typeof MomasAlert !== 'undefined') {
              MomasAlert.danger(
                'Logout from Admin Console?',
                'Are you sure you want to log out of MomasPay Admin?',
                'Yes, Logout',
                () => {
                  window.location.href = '../index.html';
                }
              );
            } else {
              if (confirm('Are you sure you want to logout?')) {
                window.location.href = '../index.html';
              }
            }
          });
        }
      }

      const isActive = dropdown.classList.contains('active');
      
      // Close all active dropdowns
      document.querySelectorAll('.admin-profile-dropdown-menu').forEach(d => d.classList.remove('active'));
      document.querySelectorAll('.admin-profile-pill').forEach(p => p.classList.remove('active'));

      if (!isActive) {
        dropdown.classList.add('active');
        profilePill.classList.add('active');
      }
    } else {
      // Close dropdown if clicking outside
      if (!e.target.closest('.admin-profile-dropdown-menu')) {
        document.querySelectorAll('.admin-profile-dropdown-menu').forEach(d => d.classList.remove('active'));
        document.querySelectorAll('.admin-profile-pill').forEach(p => p.classList.remove('active'));
      }
    }
  });

  // MomasPay SweetAlert Notification System for Admin Portal
  window.MomasAlert = {
    success: (title, text, callback) => {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'success',
          title: title || 'Success!',
          text: text || 'Operation completed successfully.',
          confirmButtonColor: '#00C868',
          customClass: { popup: 'momas-swal-popup' }
        }).then(() => {
          if (callback) callback();
        });
      } else {
        alert(`${title}\n${text}`);
        if (callback) callback();
      }
    },
    warning: (title, text, confirmText, callback) => {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: title || 'Are you sure?',
          text: text || 'Please confirm your action.',
          showCancelButton: true,
          confirmButtonColor: '#00C868',
          cancelButtonColor: '#94A3B8',
          confirmButtonText: confirmText || 'Yes, proceed',
          customClass: { popup: 'momas-swal-popup' }
        }).then((result) => {
          if (result.isConfirmed && callback) callback();
        });
      } else {
        if (confirm(`${title}\n${text}`) && callback) callback();
      }
    },
    danger: (title, text, confirmText, callback) => {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: title || 'Confirm Action?',
          text: text || 'This action cannot be undone.',
          showCancelButton: true,
          confirmButtonColor: '#EF4444',
          cancelButtonColor: '#64748B',
          confirmButtonText: confirmText || 'Yes, proceed',
          customClass: { popup: 'momas-swal-popup' }
        }).then((result) => {
          if (result.isConfirmed && callback) callback();
        });
      } else {
        if (confirm(`${title}\n${text}`) && callback) callback();
      }
    }
  };

  // Intercept Admin Button Click Actions for SweetAlert
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('button, .btn-action-escalate, .btn-action-close');
    if (!btn) return;

    const text = btn.textContent.trim();

    if (text.includes('Escalate')) {
      e.preventDefault();
      MomasAlert.warning(
        'Escalate Issue?',
        'This support ticket will be escalated to Senior Engineering.',
        'Yes, Escalate',
        () => {
          MomasAlert.success('Ticket Escalated!', 'The ticket has been successfully escalated to Senior Support.');
        }
      );
    } else if (text.includes('Close Ticket') || text.includes('Close Issue')) {
      e.preventDefault();
      MomasAlert.danger(
        'Close Support Ticket?',
        'Are you sure you want to mark this issue as resolved and closed?',
        'Yes, Close Ticket',
        () => {
          MomasAlert.success('Ticket Closed!', 'The issue ticket has been marked as resolved.', () => {
            window.location.href = 'admin-logged-issues.html';
          });
        }
      );
    } else if (text.includes('Deactivate Estate') || text.includes('Delete Estate')) {
      e.preventDefault();
      MomasAlert.danger(
        'Deactivate Estate?',
        'Are you sure you want to set this estate to inactive status?',
        'Yes, Deactivate',
        () => {
          MomasAlert.success('Estate Deactivated', 'The estate has been deactivated successfully.', () => {
            window.location.href = 'admin-estate.html';
          });
        }
      );
    } else if (text.includes('Revoke')) {
      e.preventDefault();
      MomasAlert.danger(
        'Revoke Access Code?',
        'This visitor access token will be invalidated immediately.',
        'Yes, Revoke',
        () => {
          MomasAlert.success('Token Revoked!', 'The access code has been invalidated.');
        }
      );
    } else if (text.includes('Clear Tamper Token')) {
      e.preventDefault();
      MomasAlert.warning(
        'Generate Clear Tamper Token?',
        'Generate STS 20-digit tamper clear code for this meter.',
        'Generate Token',
        () => {
          MomasAlert.success('Tamper Code Generated!', 'STS Clear Code: 4912-8834-0192-5510-9923');
        }
      );
    }
  });

  // Intercept Form Submissions in Admin Portal for SweetAlert
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', (e) => {
      if (document.querySelector('.admin-layout')) {
        e.preventDefault();
        const actionUrl = form.getAttribute('action') || 'admin-estate.html';
        MomasAlert.success('Saved Successfully!', 'All details have been safely recorded on MomasPay.', () => {
          window.location.href = actionUrl;
        });
      }
    });
  });

  // ==========================================================================
  // DATATABLES & DYNAMIC DATA INSERTION ENGINE FOR ALL ADMIN TABLES
  // ==========================================================================
  function initMomasDataTables() {
    const adminTables = document.querySelectorAll('.admin-table');
    adminTables.forEach(table => {
      // 1. Add select-all checkbox column header if not present
      const headerRow = table.querySelector('thead tr');
      if (headerRow && !headerRow.querySelector('.select-all-header')) {
        const th = document.createElement('th');
        th.className = 'select-all-header';
        th.style.width = '38px';
        th.style.textAlign = 'center';
        th.innerHTML = `<input type="checkbox" class="table-checkbox select-all-checkbox" title="Select All Rows">`;
        headerRow.insertBefore(th, headerRow.firstChild);

        // Add checkbox cell to every existing table row
        const bodyRows = table.querySelectorAll('tbody tr');
        bodyRows.forEach(row => {
          if (!row.querySelector('.row-select-cell')) {
            const td = document.createElement('td');
            td.className = 'row-select-cell';
            td.style.textAlign = 'center';
            td.innerHTML = `<input type="checkbox" class="table-checkbox row-select-checkbox">`;
            row.insertBefore(td, row.firstChild);
          }
        });
      }

      // 2. Add sorting indicators & column sorting click events
      const ths = table.querySelectorAll('thead th:not(.select-all-header)');
      ths.forEach((th, idx) => {
        th.classList.add('sorting');
        th.addEventListener('click', () => {
          const isAsc = th.classList.contains('sorting_asc');
          ths.forEach(t => t.classList.remove('sorting_asc', 'sorting_desc'));
          th.classList.add(isAsc ? 'sorting_desc' : 'sorting_asc');
          sortTableByColumn(table, idx + 1, !isAsc);
        });
      });

      // 3. Inject "Show X Entries" dropdown into card header if not present
      const cardHeader = table.closest('.admin-content-card')?.querySelector('.admin-card-header');
      if (cardHeader && !cardHeader.querySelector('.datatable-length-wrapper')) {
        const lengthWrap = document.createElement('div');
        lengthWrap.className = 'datatable-length-wrapper';
        lengthWrap.style.display = 'flex';
        lengthWrap.style.alignItems = 'center';
        lengthWrap.style.gap = '8px';
        lengthWrap.style.fontSize = '0.82rem';
        lengthWrap.style.fontWeight = '600';
        lengthWrap.style.color = '#64748B';
        lengthWrap.innerHTML = `
          <span>Show</span>
          <select class="datatable-length-select">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span>entries</span>
        `;
        cardHeader.insertBefore(lengthWrap, cardHeader.firstChild);

        // Bind entries length selector
        const select = lengthWrap.querySelector('.datatable-length-select');
        select.addEventListener('change', () => {
          const limit = parseInt(select.value, 10);
          const rows = table.querySelectorAll('tbody tr');
          rows.forEach((row, rIdx) => {
            row.style.display = rIdx < limit ? '' : 'none';
          });
          const paginationInfo = table.closest('.admin-content-card')?.querySelector('.admin-table-pagination div:first-child');
          if (paginationInfo) {
            paginationInfo.textContent = `Showing 1 to ${Math.min(limit, rows.length)} of ${rows.length} entries`;
          }
        });
      }

      // 4. Inject "+ Add Record" button if table card has header action controls
      if (cardHeader && !cardHeader.querySelector('.btn-add-record-trigger')) {
        const actionWrap = cardHeader.querySelector('div:last-child') || cardHeader;
        const addBtn = document.createElement('button');
        addBtn.type = 'button';
        addBtn.className = 'btn-action-view btn-add-record-trigger';
        addBtn.style.padding = '8px 16px';
        addBtn.style.background = 'linear-gradient(135deg, #00C868 0%, #009650 100%)';
        addBtn.style.color = '#FFFFFF';
        addBtn.style.fontWeight = '800';
        addBtn.innerHTML = `➕ Insert Record`;
        actionWrap.appendChild(addBtn);

        addBtn.addEventListener('click', () => openDynamicInsertModal(table));
      }
    });

    // Checkbox Row Select & Select All Event Delegation
    document.addEventListener('change', (e) => {
      if (e.target.classList.contains('select-all-checkbox')) {
        const table = e.target.closest('table');
        if (!table) return;
        const checkboxes = table.querySelectorAll('.row-select-checkbox');
        checkboxes.forEach(cb => {
          cb.checked = e.target.checked;
          const row = cb.closest('tr');
          if (row) {
            if (e.target.checked) row.classList.add('selected-row');
            else row.classList.remove('selected-row');
          }
        });
      } else if (e.target.classList.contains('row-select-checkbox')) {
        const row = e.target.closest('tr');
        if (row) {
          if (e.target.checked) row.classList.add('selected-row');
          else row.classList.remove('selected-row');
        }
      }
    });

    // Live Search Filter Handler for Admin Tables
    document.addEventListener('keyup', (e) => {
      if (e.target.matches('#table-search-input, .admin-table-search-input')) {
        const query = e.target.value.toLowerCase().trim();
        const container = e.target.closest('.admin-content-card') || document;
        const rows = container.querySelectorAll('.admin-table tbody tr');
        let matchCount = 0;
        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const isMatch = text.includes(query);
          row.style.display = isMatch ? '' : 'none';
          if (isMatch) matchCount++;
        });

        const paginationInfo = container.querySelector('.admin-table-pagination div:first-child');
        if (paginationInfo) {
          paginationInfo.textContent = `Showing 1 to ${matchCount} of ${rows.length} entries`;
        }
      }
    });
  }

  // Column Sort Logic
  function sortTableByColumn(table, colIdx, asc = true) {
    const tbody = table.querySelector('tbody');
    if (!tbody) return;
    const rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort((a, b) => {
      const cellA = a.children[colIdx]?.textContent.trim().toLowerCase() || '';
      const cellB = b.children[colIdx]?.textContent.trim().toLowerCase() || '';

      const numA = parseFloat(cellA.replace(/[^0-9.-]+/g, ''));
      const numB = parseFloat(cellB.replace(/[^0-9.-]+/g, ''));

      if (!isNaN(numA) && !isNaN(numB)) {
        return asc ? numA - numB : numB - numA;
      }
      return asc ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
    });

    rows.forEach(r => tbody.appendChild(r));
  }

  // Open Dynamic Insert Record Modal
  function openDynamicInsertModal(table) {
    let modalOverlay = document.getElementById('momas-dynamic-insert-modal');
    if (!modalOverlay) {
      modalOverlay = document.createElement('div');
      modalOverlay.id = 'momas-dynamic-insert-modal';
      modalOverlay.className = 'momas-modal-overlay';
      modalOverlay.innerHTML = `
        <div class="momas-modal-card">
          <div class="momas-modal-header">
            <div class="momas-modal-title">Insert New Table Record</div>
            <button type="button" class="momas-modal-close" onclick="closeDynamicInsertModal()">&times;</button>
          </div>
          <form id="momas-dynamic-insert-form">
            <div class="momas-modal-body" id="momas-modal-form-fields"></div>
            <div class="momas-modal-footer">
              <button type="button" class="btn-action-view" onclick="closeDynamicInsertModal()" style="background: #E2E8F0; color: #475569;">Cancel</button>
              <button type="submit" class="btn-action-view" style="background: #00A859; color: #FFF; font-weight: 800;">Submit Record</button>
            </div>
          </form>
        </div>
      `;
      document.body.appendChild(modalOverlay);

      modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) closeDynamicInsertModal();
      });
    }

    // Build form fields based on table column headers
    const formFields = modalOverlay.querySelector('#momas-modal-form-fields');
    formFields.innerHTML = '';
    const headers = Array.from(table.querySelectorAll('thead th:not(.select-all-header):not(:last-child)'));

    headers.forEach((th, idx) => {
      const fieldName = th.textContent.trim();
      const group = document.createElement('div');
      group.className = 'form-group';

      let inputHTML = '';
      if (fieldName.toLowerCase().includes('status')) {
        inputHTML = `
          <select class="input-field dynamic-insert-input" required style="height: 48px; border-radius: 10px;">
            <option value="Active">Active</option>
            <option value="Pending">Pending</option>
            <option value="Completed">Completed</option>
            <option value="Resolved">Resolved</option>
          </select>
        `;
      } else if (fieldName.toLowerCase().includes('date') || fieldName.toLowerCase().includes('time')) {
        inputHTML = `<input type="text" class="input-field dynamic-insert-input" value="${new Date().toISOString().slice(0,10)} 12:00:00" required style="height: 48px; border-radius: 10px;">`;
      } else {
        inputHTML = `<input type="text" class="input-field dynamic-insert-input" placeholder="Enter ${fieldName}..." required style="height: 48px; border-radius: 10px;">`;
      }

      group.innerHTML = `
        <label class="form-label" style="font-weight: 700; color: #1E293B;">${fieldName}</label>
        <div class="input-container">${inputHTML}</div>
      `;
      formFields.appendChild(group);
    });

    // Handle dynamic form submit
    const form = modalOverlay.querySelector('#momas-dynamic-insert-form');
    form.onsubmit = (e) => {
      e.preventDefault();
      const inputs = Array.from(formFields.querySelectorAll('.dynamic-insert-input'));
      const tbody = table.querySelector('tbody');

      if (tbody) {
        const tr = document.createElement('tr');
        tr.style.background = '#ECFDF5';
        
        let rowHTML = `<td class="row-select-cell" style="text-align: center;"><input type="checkbox" class="table-checkbox row-select-checkbox"></td>`;
        inputs.forEach(inp => {
          let val = inp.value.trim();
          if (val.toLowerCase() === 'active' || val.toLowerCase() === 'completed' || val.toLowerCase() === 'resolved') {
            rowHTML += `<td><span class="status-pill status-active">${val}</span></td>`;
          } else if (val.toLowerCase() === 'pending') {
            rowHTML += `<td><span class="status-pill status-pending">${val}</span></td>`;
          } else if (val.startsWith('₦')) {
            rowHTML += `<td style="font-weight: 700; color: #00A859;">${val}</td>`;
          } else {
            rowHTML += `<td style="font-weight: 600;">${val}</td>`;
          }
        });

        // Add action column if table has actions
        const hasActionCol = table.querySelector('thead tr').lastElementChild.textContent.trim().toLowerCase().includes('action');
        if (hasActionCol) {
          rowHTML += `<td><button class="btn-action-view" type="button" onclick="MomasAlert.success('Record Saved', 'Record details updated.')">View Details</button></td>`;
        }

        tr.innerHTML = rowHTML;
        tbody.insertBefore(tr, tbody.firstChild);

        closeDynamicInsertModal();

        if (typeof MomasAlert !== 'undefined') {
          MomasAlert.success('Record Inserted!', 'New row entry successfully inserted into DataTables.');
        } else {
          alert('Record Inserted Successfully!');
        }

        // Highlight new row
        setTimeout(() => {
          tr.style.transition = 'background 1s ease';
          tr.style.background = '';
        }, 1200);
      }
    };

    modalOverlay.classList.add('active');
  }

  window.closeDynamicInsertModal = function() {
    const modalOverlay = document.getElementById('momas-dynamic-insert-modal');
    if (modalOverlay) modalOverlay.classList.remove('active');
  };

  // Initialize DataTables across all admin portal tables
  initMomasDataTables();
});

// Dedicated Isolated Card Printing Function
window.printCardOnly = function(cardId) {
  const card = document.getElementById(cardId);
  if (!card) {
    window.print();
    return;
  }

  // Clone printable card element
  const clone = card.cloneNode(true);
  
  // Remove all .no-print elements from the cloned node
  clone.querySelectorAll('.no-print').forEach(el => el.remove());

  // Create isolated iframe for print execution
  let iframe = document.getElementById('print-receipt-iframe');
  if (iframe) iframe.remove();
  
  iframe = document.createElement('iframe');
  iframe.id = 'print-receipt-iframe';
  iframe.style.position = 'fixed';
  iframe.style.right = '0';
  iframe.style.bottom = '0';
  iframe.style.width = '0';
  iframe.style.height = '0';
  iframe.style.border = '0';
  document.body.appendChild(iframe);

  const doc = iframe.contentWindow.document;
  doc.open();
  doc.write(`
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Official Vending Receipt</title>
      <style>
        * {
          box-sizing: border-box;
          margin: 0;
          padding: 0;
          font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        body {
          background: #FFFFFF !important;
          color: #1E293B !important;
          padding: 20px !important;
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
        }
        .printable-card, .admin-content-card {
          margin: 0 auto !important;
          max-width: 600px !important;
          box-shadow: none !important;
          border: 1px solid #CBD5E1 !important;
          border-radius: 12px !important;
          background: #FFFFFF !important;
          padding: 24px;
        }
        .admin-card-header {
          display: none !important;
        }
      </style>
    </head>
    <body>
      ${clone.outerHTML}
      <script>
        window.onload = function() {
          setTimeout(function() {
            window.focus();
            window.print();
          }, 250);
        };
      </script>
    </body>
    </html>
  `);
  doc.close();

  setTimeout(() => {
    if (iframe) iframe.remove();
  }, 4000);
};







