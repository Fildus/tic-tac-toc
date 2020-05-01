// import Autocomplete from "autocompleter";
//
// const dataAutocompleteUrlClass = 'data-autocomplete-url'
// const fieldCollectionDeleteButtonClass = 'field-collection-delete-button'
// const fieldCollectionAddButtonClass = 'field-collection-add-button'
//
// const profiles = ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_CAROTTE'];
//
// let inputs = []
// let autocompleteElements = []
//
// function deleteButtons() {
//     document.querySelector(`[${dataAutocompleteUrlClass}]`)
//         .parentNode
//         .parentNode
//         .querySelectorAll(`.${fieldCollectionDeleteButtonClass}`)
//         .forEach(function (e) {
//             e.addEventListener('click', function () {
//                 setTimeout(reboot, 100)
//             })
//         })
// }
//
// async function reboot() {
//     await destroy()
//     await deleteButtons()
//     await run()
// }
//
// async function run() {
//     inputs = Array.prototype.slice.call(
//         document.querySelector(`[${dataAutocompleteUrlClass}]`).querySelectorAll('input')
//     );
//
//     inputs.forEach(function (e) {
//         e.setAttribute('autocomplete', 'off')
//
//         let autoItem = Autocomplete({
//             input: e,
//             minLength: 1,
//             emptyMsg: 'Aucun élément trouvé',
//             fetch: function (text, update) {
//                 text = text.toLowerCase();
//                 let suggestions = profiles.filter(n => n.toLowerCase().match(text));
//                 update(suggestions);
//             },
//             onSelect: function (item) {
//                 e.value = item.toString();
//                 e.setAttribute('value', item.toString())
//             },
//             render: function (item, currentValue) {
//                 const itemElement = document.createElement("div");
//                 itemElement.textContent = item.toString();
//                 return itemElement;
//             }
//         })
//
//         autocompleteElements.push(autoItem)
//     });
// }
//
// async function destroy() {
//     await autocompleteElements.forEach(function (e) {
//         inputs = []
//         e.destroy()
//     })
// }
//
// async function autocomplete() {
//     const dataAutocomplete = document.querySelector(`[${dataAutocompleteUrlClass}]`)
//
//     if (dataAutocomplete !== null) {
//         const button = dataAutocomplete
//             .parentNode
//             .parentNode
//             .querySelector(`.${fieldCollectionAddButtonClass}`)
//
//         button.addEventListener('click', function () {
//             setTimeout(reboot, 100)
//         })
//
//         button.setAttribute('type', 'button')
//
//         await deleteButtons()
//         await run()
//     }
// }
//
// export default autocomplete
