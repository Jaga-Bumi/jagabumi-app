/**
 * Live Form Validation System
 * Provides real-time validation feedback for forms
 */

class FormValidator {
  constructor(formId, rules) {
    this.form = document.getElementById(formId);
    this.rules = rules;
    this.errors = {};
    this.init();
  }

  init() {
    if (!this.form) return;

    Object.keys(this.rules).forEach(fieldName => {
      const field = this.form.querySelector(`[name="${fieldName}"]`);
      if (field) {
        field.addEventListener('blur', () => this.validateField(fieldName));
        field.addEventListener('input', () => this.clearError(fieldName));
      }
    });

    this.form.addEventListener('submit', (e) => {
      if (!this.validateAll()) {
        e.preventDefault();
      }
    });
  }

  validateField(fieldName) {
    const field = this.form.querySelector(`[name="${fieldName}"]`);
    if (!field) return true;

    const value = field.value.trim();
    const fieldRules = this.rules[fieldName];
    let isValid = true;
    let errorMessage = '';

    // Required validation
    if (fieldRules.required && !value) {
      isValid = false;
      errorMessage = fieldRules.messages?.required || `${this.formatFieldName(fieldName)} is required.`;
    }

    // Min length validation
    if (isValid && fieldRules.min && value.length < fieldRules.min) {
      isValid = false;
      errorMessage = fieldRules.messages?.min || `${this.formatFieldName(fieldName)} must be at least ${fieldRules.min} characters.`;
    }

    // Max length validation
    if (isValid && fieldRules.max && value.length > fieldRules.max) {
      isValid = false;
      errorMessage = fieldRules.messages?.max || `${this.formatFieldName(fieldName)} cannot exceed ${fieldRules.max} characters.`;
    }

    // Email validation
    if (isValid && fieldRules.email && value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        isValid = false;
        errorMessage = fieldRules.messages?.email || 'Please enter a valid email address.';
      }
    }

    // URL validation
    if (isValid && fieldRules.url && value) {
      try {
        new URL(value);
      } catch {
        isValid = false;
        errorMessage = fieldRules.messages?.url || 'Please enter a valid URL.';
      }
    }

    // In validation (enum)
    if (isValid && fieldRules.in && value && !fieldRules.in.includes(value)) {
      isValid = false;
      errorMessage = fieldRules.messages?.in || `Please select a valid ${this.formatFieldName(fieldName)}.`;
    }

    // File validation
    if (isValid && fieldRules.file && field.files && field.files[0]) {
      const file = field.files[0];
      
      // Check file type
      if (fieldRules.mimes) {
        const extension = file.name.split('.').pop().toLowerCase();
        if (!fieldRules.mimes.includes(extension)) {
          isValid = false;
          errorMessage = fieldRules.messages?.mimes || `File must be one of: ${fieldRules.mimes.join(', ')}`;
        }
      }

      // Check file size (in KB)
      if (fieldRules.maxSize && file.size > fieldRules.maxSize * 1024) {
        isValid = false;
        errorMessage = fieldRules.messages?.maxSize || `File size cannot exceed ${fieldRules.maxSize}KB`;
      }

      // Image validation
      if (fieldRules.image && !file.type.startsWith('image/')) {
        isValid = false;
        errorMessage = fieldRules.messages?.image || 'File must be an image.';
      }
    }

    // Custom pattern validation
    if (isValid && fieldRules.pattern && value) {
      const regex = new RegExp(fieldRules.pattern);
      if (!regex.test(value)) {
        isValid = false;
        errorMessage = fieldRules.messages?.pattern || `${this.formatFieldName(fieldName)} format is invalid.`;
      }
    }

    if (!isValid) {
      this.showError(fieldName, errorMessage);
      this.errors[fieldName] = errorMessage;
    } else {
      this.clearError(fieldName);
      delete this.errors[fieldName];
    }

    return isValid;
  }

  validateAll() {
    let isValid = true;
    Object.keys(this.rules).forEach(fieldName => {
      if (!this.validateField(fieldName)) {
        isValid = false;
      }
    });
    return isValid;
  }

  showError(fieldName, message) {
    const field = this.form.querySelector(`[name="${fieldName}"]`);
    if (!field) return;

    // Remove existing error
    this.clearError(fieldName);

    // Add error class to field
    field.classList.add('border-destructive', 'focus:ring-destructive');
    field.classList.remove('border-border', 'focus:ring-primary');

    // Create and insert error message
    const errorDiv = document.createElement('p');
    errorDiv.className = 'mt-2 text-sm text-destructive';
    errorDiv.id = `error-${fieldName}`;
    errorDiv.textContent = message;

    field.parentElement.appendChild(errorDiv);
  }

  clearError(fieldName) {
    const field = this.form.querySelector(`[name="${fieldName}"]`);
    if (!field) return;

    // Remove error classes
    field.classList.remove('border-destructive', 'focus:ring-destructive');
    field.classList.add('border-border', 'focus:ring-primary');

    // Remove error message
    const errorDiv = document.getElementById(`error-${fieldName}`);
    if (errorDiv) {
      errorDiv.remove();
    }
  }

  formatFieldName(fieldName) {
    return fieldName
      .replace(/_/g, ' ')
      .replace(/\b\w/g, l => l.toUpperCase());
  }

  updateCharacterCount(fieldName, maxLength) {
    const field = this.form.querySelector(`[name="${fieldName}"]`);
    if (!field) return;

    const counterId = `char-count-${fieldName}`;
    let counter = document.getElementById(counterId);

    if (!counter) {
      counter = document.createElement('p');
      counter.id = counterId;
      counter.className = 'mt-1 text-xs text-muted-foreground text-right';
      field.parentElement.appendChild(counter);
    }

    field.addEventListener('input', () => {
      const remaining = maxLength - field.value.length;
      counter.textContent = `${field.value.length}/${maxLength} characters`;
      counter.className = remaining < 0 
        ? 'mt-1 text-xs text-destructive text-right'
        : 'mt-1 text-xs text-muted-foreground text-right';
    });

    // Trigger initial count
    field.dispatchEvent(new Event('input'));
  }
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
  module.exports = FormValidator;
}
