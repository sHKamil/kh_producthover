/**
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

async function fetchAPI(url, method, data) {
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: data ? JSON.stringify(data) : undefined,
        });
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const result = await response.json();
        return result;
    }
    catch (error) {
        // Handle errors
        console.error('Error:', error);
    }
}


const changeImage = (link, img) => {
    if (img) {
        link += img.src.split('/').pop(); // Add image name to the link
        img.src = link;
        if (img.hasAttribute("srcset")) {
            img.removeAttribute("srcset");
        }
    }
};

const addHoverEffect = (product) => {
    let cards = document.querySelectorAll('[data-id-product="' + product.product_id + '"]');
    if (cards) {
        cards.forEach(card => {

            let img = card.querySelector('img');
            img.addEventListener("mouseover", async () => {
                if(typeof product.id_image !== 'undefined') {
                    changeImage('/' + product.id_image[0] + '-large_default/', img);
                }
            });
            img.addEventListener("mouseout", async () => {
                changeImage('/' + product.cover_id + '-large_default/', img);
            });
        });
    }
};

const makeCoversReactive = (apiAddress) => {
    fetchAPI(apiAddress, 'GET').then((result) => {
        result.forEach(product => addHoverEffect(product));
    });
    return;
};

const gatherProductIds = () => {
    let products = document.querySelectorAll("[data-id-product]");
    if (products.length > 0) {
        let ids = "";
        products.forEach(el => {
            ids += "," + el.getAttribute('data-id-product');
        });
        ids = ids.slice(1);
        makeCoversReactive('/module/kh_producthover/GetImages?method=getSelected&ids=' + ids);
    }
};
gatherProductIds();
let page_products = document.getElementById('products');
if (page_products) {
    let observer = new MutationObserver(gatherProductIds);
    observer.observe(page_products, { childList: true });
}
