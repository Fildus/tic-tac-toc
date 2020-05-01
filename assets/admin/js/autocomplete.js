import Autocomplete from "autocompleter";

let dataAutocompleteUrlAttr = ''
let dataAutocompleteUrl = ''

const fieldCollectionDeleteButtonClass = 'field-collection-delete-button'
const fieldCollectionAddButtonClass = 'field-collection-add-button'
let inputs = []
let autocompleteElements = []

function isJson(item) {
    item = typeof item !== "string"
        ? JSON.stringify(item)
        : item;

    try {
        item = JSON.parse(item);
    } catch (e) {
        return false;
    }

    return typeof item === "object" && item !== null;
}

function deleteButtons() {
    document.querySelector(`[${dataAutocompleteUrlAttr}]`)
        .parentNode
        .parentNode
        .querySelectorAll(`.${fieldCollectionDeleteButtonClass}`)
        .forEach(function (e) {
            e.addEventListener('click', function () {
                setTimeout(reboot, 100)
            })
        })
}

async function reboot() {
    await destroy()
    await deleteButtons()
    await run()
}

async function run() {
    inputs = Array.prototype.slice.call(
        document.querySelector(`[${dataAutocompleteUrlAttr}]`).querySelectorAll('input')
    );

    inputs.forEach(function (e) {
        e.setAttribute('autocomplete', 'off')

        let autoItem = Autocomplete({
            input: e,
            minLength: 1,
            showOnFocus: true,
            emptyMsg: 'Aucun élément trouvé',
            fetch: function (text, update) {
                fetch(`${dataAutocompleteUrl}/${text}`, {method: 'get'})
                    .then(function (response) {
                        let contentType = response.headers.get("content-type");

                        if (contentType && contentType.indexOf("application/json") !== -1) {
                            response.json().then(e => update(e))
                        } else {
                            console.log("Oops, nous n'avons pas du JSON!");
                        }
                    });
            },
            onSelect: function (item) {
                e.value = item.toString();
                e.setAttribute('value', item.toString())
            },
            render: function (item, currentValue) {
                const itemElement = document.createElement("div");
                itemElement.textContent = item.toString();
                return itemElement;
            }
        })

        autocompleteElements.push(autoItem)
    });
}

async function destroy() {
    await autocompleteElements.forEach(function (e) {
        inputs = []
        e.destroy()
    })
}

function autocomplete(autocompleteUrl) {
    dataAutocompleteUrlAttr = autocompleteUrl
    const dataAutocomplete = document.querySelector(`[${dataAutocompleteUrlAttr}]`)

    if (dataAutocomplete !== null) {
        dataAutocompleteUrl = dataAutocomplete.getAttribute(`${dataAutocompleteUrlAttr}`)

        const button = dataAutocomplete
            .parentNode
            .parentNode
            .querySelector(`.${fieldCollectionAddButtonClass}`)

        button.addEventListener('click', function () {
            setTimeout(reboot, 100)
        })

        button.setAttribute('type', 'button')

        deleteButtons()
        run().then()
    }
}

export default autocomplete
