/**
 * API Test Suite untuk Mosque Search Feature
 * 
 * Jalankan tests ini untuk memastikan API bekerja dengan baik
 * Usage: 
 * - Browser Console: paste dan jalankan seluruh script
 * - Atau setup dengan Jest/Vitest untuk automated testing
 */

// Configuration
const API_BASE_URL = 'http://localhost:8000/api';
const TIMEOUT = 5000;

// Test utilities
class TestRunner {
  constructor() {
    this.results = [];
    this.total = 0;
    this.passed = 0;
    this.failed = 0;
  }

  async run(testName, testFn) {
    this.total++;
    console.log(`\n⏳ Running: ${testName}`);
    
    try {
      await testFn();
      this.passed++;
      console.log(`✅ PASSED: ${testName}`);
      this.results.push({ name: testName, status: 'PASSED' });
    } catch (error) {
      this.failed++;
      console.error(`❌ FAILED: ${testName}`);
      console.error(`Error: ${error.message}`);
      this.results.push({ name: testName, status: 'FAILED', error: error.message });
    }
  }

  printSummary() {
    console.log('\n' + '='.repeat(60));
    console.log('📊 TEST SUMMARY');
    console.log('='.repeat(60));
    console.log(`Total Tests: ${this.total}`);
    console.log(`✅ Passed: ${this.passed}`);
    console.log(`❌ Failed: ${this.failed}`);
    console.log(`Success Rate: ${((this.passed / this.total) * 100).toFixed(2)}%`);
    console.log('='.repeat(60));
  }
}

// Assertion helpers
async function fetchAPI(endpoint, options = {}) {
  const response = await fetch(`${API_BASE_URL}${endpoint}`, {
    method: options.method || 'GET',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...options.headers
    },
    ...options
  });

  const data = await response.json();
  return { response, data };
}

function assert(condition, message) {
  if (!condition) {
    throw new Error(`Assertion failed: ${message}`);
  }
}

function assertEqual(actual, expected, message) {
  if (actual !== expected) {
    throw new Error(`${message}\nExpected: ${expected}\nActual: ${actual}`);
  }
}

function assertExists(value, message) {
  if (value === null || value === undefined) {
    throw new Error(`${message} - value does not exist`);
  }
}

// Test Suite
async function runTestSuite() {
  const runner = new TestRunner();

  // Test 1: API Health Check
  await runner.run('API Health Check', async () => {
    try {
      const response = await fetch(`${API_BASE_URL}/mosques`);
      assert(response.ok || response.status === 200, 'API should be accessible');
    } catch (error) {
      throw new Error('API is not accessible: ' + error.message);
    }
  });

  // Test 2: Get List Mosques - Basic
  await runner.run('GET /api/mosques - Basic List', async () => {
    const { response, data } = await fetchAPI('/mosques');
    assert(response.ok, 'Response should be successful');
    assert(data.success === true, 'Response should have success flag');
    assert(data.data && Array.isArray(data.data.items), 'Response should have items array');
    assert(data.data.meta, 'Response should have pagination meta');
  });

  // Test 3: Get List Mosques - With Pagination
  await runner.run('GET /api/mosques - With Pagination', async () => {
    const { data } = await fetchAPI('/mosques?page=1&per_page=5');
    assert(data.data.meta.per_page === 5, 'Should respect per_page parameter');
    assert(data.data.meta.current_page === 1, 'Should show current page');
    assert(data.data.items.length <= 5, 'Should not exceed per_page limit');
  });

  // Test 4: Search Functionality
  await runner.run('GET /api/mosques - Search Functionality', async () => {
    const { data } = await fetchAPI('/mosques?search=masjid');
    assert(Array.isArray(data.data.items), 'Should return array of results');
    if (data.data.items.length > 0) {
      const item = data.data.items[0];
      assert(
        item.name.toLowerCase().includes('masjid') || 
        (item.address && item.address.toLowerCase().includes('masjid')),
        'Search results should match query'
      );
    }
  });

  // Test 5: Filter by Type - MASJID
  await runner.run('GET /api/mosques - Filter by Type (MASJID)', async () => {
    const { data } = await fetchAPI('/mosques?type=MASJID');
    if (data.data.items.length > 0) {
      data.data.items.forEach(item => {
        assert(item.type === 'MASJID', 'All items should be MASJID');
      });
    }
  });

  // Test 6: Filter by Type - MUSHOLLA
  await runner.run('GET /api/mosques - Filter by Type (MUSHOLLA)', async () => {
    const { data } = await fetchAPI('/mosques?type=MUSHOLLA');
    if (data.data.items.length > 0) {
      data.data.items.forEach(item => {
        assert(item.type === 'MUSHOLLA', 'All items should be MUSHOLLA');
      });
    }
  });

  // Test 7: Sort by Name
  await runner.run('GET /api/mosques - Sort by Name', async () => {
    const { data } = await fetchAPI('/mosques?sort_by=name&per_page=10');
    assert(data.data.items.length > 0, 'Should return results');
    // Check if sorted (basic check)
    for (let i = 1; i < data.data.items.length; i++) {
      assert(
        data.data.items[i].name >= data.data.items[i-1].name,
        'Items should be sorted by name'
      );
    }
  });

  // Test 8: Sort by Completion
  await runner.run('GET /api/mosques - Sort by Completion', async () => {
    const { data } = await fetchAPI('/mosques?sort_by=completion&per_page=10');
    assert(data.data.items.length > 0, 'Should return results');
    // Check if sorted descending by completion
    for (let i = 1; i < Math.min(3, data.data.items.length); i++) {
      assert(
        data.data.items[i].completion_percentage <= data.data.items[i-1].completion_percentage,
        'Items should be sorted by completion (descending)'
      );
    }
  });

  // Test 9: Min Completion Filter
  await runner.run('GET /api/mosques - Min Completion Filter', async () => {
    const minCompletion = 50;
    const { data } = await fetchAPI(`/mosques?min_completion=${minCompletion}`);
    if (data.data.items.length > 0) {
      data.data.items.forEach(item => {
        assert(
          item.completion_percentage >= minCompletion,
          `Completion should be >= ${minCompletion}`
        );
      });
    }
  });

  // Test 10: Get Mosque Detail
  await runner.run('GET /api/mosques/{id} - Get Detail', async () => {
    // First get a mosque ID
    const listData = await fetchAPI('/mosques?per_page=1');
    assert(listData.data.data.items.length > 0, 'Should have at least one mosque');
    
    const mosqueId = listData.data.data.items[0].id;
    const { response, data } = await fetchAPI(`/mosques/${mosqueId}`);
    
    assert(response.ok, 'Detail request should be successful');
    assert(data.success === true, 'Response should be successful');
    assert(data.data.id === mosqueId, 'Should return correct mosque');
    assert(data.data.name, 'Should have name');
    assert(data.data.type, 'Should have type');
  });

  // Test 11: Get Mosque Detail - Full Data
  await runner.run('GET /api/mosques/{id} - Full Detail Data', async () => {
    const listData = await fetchAPI('/mosques?per_page=1');
    const mosqueId = listData.data.data.items[0].id;
    const { data } = await fetchAPI(`/mosques/${mosqueId}`);
    
    const mosque = data.data;
    assertExists(mosque.id, 'Should have id');
    assertExists(mosque.name, 'Should have name');
    assertExists(mosque.type, 'Should have type');
    assertExists(mosque.completion_percentage, 'Should have completion_percentage');
    assertExists(mosque.is_active, 'Should have is_active');
    assert(Array.isArray(mosque.facilities), 'Should have facilities array');
  });

  // Test 12: Get Mosque Detail - Invalid ID
  await runner.run('GET /api/mosques/{id} - Invalid ID (404)', async () => {
    const { response } = await fetchAPI('/mosques/99999');
    assert(response.status === 404, 'Should return 404 for invalid ID');
  });

  // Test 13: Get Mosque Facilities
  await runner.run('GET /api/mosques/{id}/facilities - Get Facilities', async () => {
    const listData = await fetchAPI('/mosques?per_page=1');
    const mosqueId = listData.data.data.items[0].id;
    const { response, data } = await fetchAPI(`/mosques/${mosqueId}/facilities`);
    
    assert(response.ok, 'Facilities request should be successful');
    assert(data.success === true, 'Response should be successful');
    assert(Array.isArray(data.data), 'Should return facilities array');
  });

  // Test 14: Combine Multiple Filters
  await runner.run('GET /api/mosques - Combined Filters', async () => {
    const { data } = await fetchAPI('/mosques?search=masjid&type=MASJID&sort_by=name&per_page=5');
    assert(Array.isArray(data.data.items), 'Should return results');
    assert(data.data.items.length <= 5, 'Should respect per_page limit');
  });

  // Test 15: Response Structure
  await runner.run('Response Structure Validation', async () => {
    const { data } = await fetchAPI('/mosques');
    
    // Check list response structure
    assert(data.hasOwnProperty('success'), 'Should have success property');
    assert(data.hasOwnProperty('message'), 'Should have message property');
    assert(data.hasOwnProperty('data'), 'Should have data property');
    assert(data.data.hasOwnProperty('items'), 'Data should have items');
    assert(data.data.hasOwnProperty('meta'), 'Data should have meta');
    
    // Check meta structure
    const meta = data.data.meta;
    assert(meta.hasOwnProperty('current_page'), 'Meta should have current_page');
    assert(meta.hasOwnProperty('per_page'), 'Meta should have per_page');
    assert(meta.hasOwnProperty('total'), 'Meta should have total');
    assert(meta.hasOwnProperty('last_page'), 'Meta should have last_page');
  });

  // Print results
  runner.printSummary();

  return runner.results;
}

// Run tests
console.log('🧪 Starting API Test Suite...\n');
console.log(`API Base URL: ${API_BASE_URL}\n`);

runTestSuite().then(results => {
  console.log('\n📋 Detailed Results:');
  results.forEach(result => {
    const icon = result.status === 'PASSED' ? '✅' : '❌';
    console.log(`${icon} ${result.name}`);
    if (result.error) {
      console.log(`   Error: ${result.error}`);
    }
  });
}).catch(error => {
  console.error('Test Suite Error:', error);
});

// Export for use in test frameworks
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { runTestSuite, TestRunner, fetchAPI, assert };
}
