// Utility functions untuk search feature
// File ini dapat di-import di component manapun yang memerlukan search functionality

export const API_BASE_URL = '/api';

/**
 * Configuration untuk search
 */
export const SEARCH_CONFIG = {
  DEFAULT_PER_PAGE: 12,
  MOSQUE_TYPES: ['MASJID', 'MUSHOLLA'],
  SORT_OPTIONS: [
    { value: 'name', label: 'Nama (A-Z)' },
    { value: 'completion', label: 'Kelengkapan Tertinggi' },
    { value: 'newest', label: 'Terbaru' }
  ],
  COLORS: {
    PRIMARY: '#1a7f8f',
    PRIMARY_DARK: '#157a8a',
    SUCCESS: '#4CAF50',
    ERROR: '#f44336',
    WARNING: '#ff9800'
  }
};

/**
 * Format currency untuk display (jika ada data harga)
 */
export function formatCurrency(value) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value);
}

/**
 * Format date untuk display
 */
export function formatDate(dateString) {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
}

/**
 * Format percentage dengan 2 decimal
 */
export function formatPercentage(value) {
  return Math.round(value * 100) / 100 + '%';
}

/**
 * Get status badge class
 */
export function getStatusBadgeClass(isActive) {
  return isActive ? 'badge-success' : 'badge-error';
}

/**
 * Get type badge class
 */
export function getTypeBadgeClass(type) {
  return type === 'MASJID' ? 'badge-masjid' : 'badge-musholla';
}

/**
 * Get completion color based on percentage
 */
export function getCompletionColor(percentage) {
  if (percentage >= 80) return '#4CAF50'; // Green
  if (percentage >= 60) return '#ff9800'; // Orange
  return '#f44336'; // Red
}

/**
 * Validate search query
 */
export function validateSearchQuery(query) {
  if (!query || query.trim().length === 0) {
    return { valid: false, error: 'Silakan masukkan kata kunci pencarian' };
  }
  if (query.length < 2) {
    return { valid: false, error: 'Minimal 2 karakter untuk pencarian' };
  }
  if (query.length > 255) {
    return { valid: false, error: 'Maksimal 255 karakter' };
  }
  return { valid: true };
}

/**
 * Build query string dari filter object
 */
export function buildQueryString(params) {
  const urlParams = new URLSearchParams();
  
  for (const [key, value] of Object.entries(params)) {
    if (value !== null && value !== undefined && value !== '') {
      urlParams.append(key, value);
    }
  }
  
  return urlParams.toString();
}

/**
 * Parse URL query params
 */
export function parseQueryParams(queryString = null) {
  const params = new URLSearchParams(queryString || window.location.search);
  const result = {};
  
  for (const [key, value] of params.entries()) {
    result[key] = value;
  }
  
  return result;
}

/**
 * Get abbreviation untuk tipe masjid
 */
export function getMosqueTypeAbbr(type) {
  return type === 'MASJID' ? 'MJD' : 'MSH';
}

/**
 * Check if API is available
 */
export async function checkApiHealth() {
  try {
    const response = await fetch('/api/health', { method: 'HEAD' });
    return response.ok;
  } catch (error) {
    return false;
  }
}

/**
 * Handle API error response
 */
export function handleApiError(error) {
  if (error.response) {
    // Server responded with error status
    const status = error.response.status;
    const message = error.response.data?.message || 'Terjadi kesalahan server';
    
    if (status === 404) {
      return 'Data tidak ditemukan';
    } else if (status === 401) {
      return 'Anda perlu login terlebih dahulu';
    } else if (status === 403) {
      return 'Anda tidak memiliki akses ke data ini';
    } else if (status === 422) {
      return 'Data yang dikirim tidak valid';
    } else if (status >= 500) {
      return 'Terjadi kesalahan server, silakan coba lagi nanti';
    }
    return message;
  } else if (error.request) {
    // Request made but no response
    return 'Tidak dapat terhubung ke server, periksa koneksi internet Anda';
  } else {
    // Error in request setup
    return error.message || 'Terjadi kesalahan yang tidak diketahui';
  }
}

/**
 * Debounce function untuk search
 */
export function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Throttle function
 */
export function throttle(func, limit) {
  let inThrottle;
  return function(...args) {
    if (!inThrottle) {
      func.apply(this, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  };
}

/**
 * Cache manager untuk API responses
 */
export class SearchCache {
  constructor(maxSize = 50) {
    this.cache = new Map();
    this.maxSize = maxSize;
  }

  set(key, value, ttl = 3600000) { // default 1 hour
    if (this.cache.size >= this.maxSize) {
      const firstKey = this.cache.keys().next().value;
      this.cache.delete(firstKey);
    }
    
    this.cache.set(key, {
      value,
      expiresAt: Date.now() + ttl
    });
  }

  get(key) {
    const item = this.cache.get(key);
    
    if (!item) return null;
    
    if (Date.now() > item.expiresAt) {
      this.cache.delete(key);
      return null;
    }
    
    return item.value;
  }

  clear() {
    this.cache.clear();
  }

  remove(key) {
    this.cache.delete(key);
  }
}

/**
 * Logger untuk debugging
 */
export class SearchLogger {
  static log(message, data = null) {
    if (process.env.NODE_ENV === 'development') {
      console.log(`[SEARCH] ${message}`, data || '');
    }
  }

  static error(message, error = null) {
    console.error(`[SEARCH ERROR] ${message}`, error || '');
  }

  static warn(message, data = null) {
    console.warn(`[SEARCH WARNING] ${message}`, data || '');
  }
}

/**
 * Analytics tracking untuk search
 */
export function trackSearchEvent(eventName, eventData = {}) {
  // Dapat diintegrasikan dengan Google Analytics, Mixpanel, etc
  const eventPayload = {
    timestamp: new Date().toISOString(),
    event: eventName,
    ...eventData
  };
  
  // Log ke console saat development
  SearchLogger.log(`Tracking event: ${eventName}`, eventPayload);
  
  // Kirim ke analytics service jika ada
  if (window.gtag) {
    window.gtag('event', eventName, eventData);
  }
}

export default {
  API_BASE_URL,
  SEARCH_CONFIG,
  formatCurrency,
  formatDate,
  formatPercentage,
  getStatusBadgeClass,
  getTypeBadgeClass,
  getCompletionColor,
  validateSearchQuery,
  buildQueryString,
  parseQueryParams,
  getMosqueTypeAbbr,
  checkApiHealth,
  handleApiError,
  debounce,
  throttle,
  SearchCache,
  SearchLogger,
  trackSearchEvent
};
