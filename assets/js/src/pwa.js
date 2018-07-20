/* eslint no-console:off */

(function () {
  'use strict'

  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/favr-pwa/sw.js').then(function (registration) { // eslint-disable-line compat/compat
        console.log('ServiceWorker registration successful with scope: ', registration.scope)
      }).catch(function (err) {
        console.log('ServiceWorker registration failed: ', err)
      })
    });
    //
    // if (self.indexedDB) {
    //   console.log('IndexedDB is supported!');
    //   var request = self.indexedDB.open('FAVRindexedDB', 1);
    //   var FavrDB;
    //
    //   request.onsuccess = function (event) {
    //     console.log('[onsuccess]', request.result);
    //     // some sample products data
    //     var products = [
    //         {id: 1, name: 'Red Men T-Shirt', price: '$3.99'},
    //         {id: 2, name: 'Pink Women Shorts', price: '$5.99'},
    //         {id: 3, name: 'Nike white Shoes', price: '$300'}
    //     ];
    //
    //     FavrDB = event.target.result;
    //
    //     var transaction = FavrDB.transaction('products', 'readwrite');
    //
    //     // transaction.onsuccess(event) {
    //     //     console.log('[Transaction] ALL DONE!')
    //     // }
    //
    //     var productsStore = transaction.objectStore('products');
    //
    //     products.forEach(function(product){
    //         var db_op_req = productsStore.add(product); // IDBRequest
    //     });
    //   };
    //
    //   request.onerror = function (event) {
    //     console.log('[onerror]', request.error);
    //   };
    //
    //   request.onupgradeneeded = function(event) {
    //       var db = event.target.result;
    //       var store = db.createObjectStore('products', {keyPath: 'id', autoIncrement: true});
    //
    //       // create unique index on keyPath === 'id'
    //       store.createIndex('products_id_unqiue', 'id', {unique: true});
    //   };
    // } else {
    //   console.log('IndexedDB is not supported!')
    // }
  } else {
    console.log('Service workers are not supported.')
  }
}())
