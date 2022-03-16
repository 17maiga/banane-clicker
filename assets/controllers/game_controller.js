import {Controller} from "@hotwired/stimulus";
import $ from "jquery";

export default class extends Controller {
    static targets = ['score', 'upgradeContainer', 'output', 'upgrade'];

    // Called at game start, fetches the game info through an AJAX request, starts the game loop, and displays all necessary info (user score, buttons to buy upgrades and save the game, and success/failure messages)
    initialize() {
        this.gain = 0;
        this.upgrades = {};

        $.ajax({
            url: '/game/initialize',
            method: 'POST',
            dataType: 'json',
            async: true,

            success: (data) => {
                this.outputTarget.textContent = 'Game loaded';
                setTimeout(() => {
                        this.outputTarget.textContent = '';
                    }, 5000
                );

                this.scoreTarget.textContent = data['score'];

                let data_upgrades = data['upgrades'];
                for (let i = 0; i < data_upgrades.length; i++) {
                    let module = document.createElement('div');
                    module.setAttribute('id', data_upgrades[i]['name']);
                    module.setAttribute('data-game-target', 'upgrade');

                    let moduleName = document.createElement('div');
                    moduleName.setAttribute('class', 'text-warning');
                    moduleName.innerText = data_upgrades[i]['name'] + ' (' + data_upgrades[i]['bps'] + ' bananas/s): ';

                    let moduleValue = document.createElement('b');
                    moduleValue.setAttribute('id', 'value-' + data_upgrades[i]['name']);
                    moduleValue.innerText = data_upgrades[i]['amount'];
                    moduleName.appendChild(moduleValue);

                    let moduleBuy = document.createElement('button');
                    moduleBuy.setAttribute('data-action', 'game#buy');
                    moduleBuy.setAttribute('data-game-name-param', data_upgrades[i]['name']);
                    moduleBuy.setAttribute('class', 'text-warning bg-dark border-warning rounded')
                    moduleBuy.appendChild(moduleName);

                    module.appendChild(moduleBuy);

                    this.upgradeContainerTarget.appendChild(module);
                    this.upgrades[data_upgrades[i]['name']] = {
                        'bps':      data_upgrades[i]['bps'],
                        'price':    data_upgrades[i]['price'],
                        'amount':   data_upgrades[i]['amount']
                    };
                    this.gain += parseInt(data_upgrades[i]['bps']) * parseInt(data_upgrades[i]['amount']);
                }
                console.log(this.upgrades)
                setInterval(() => {
                        let score = parseInt(this.scoreTarget.textContent);
                        this.scoreTarget.innerText = score + this.gain;
                    }, 1000
                );
            },
            error: () => {
                this.outputTarget.textContent = 'Failed to load game';
                setTimeout(() => {
                        this.outputTarget.textContent = '';
                    }, 5000
                );
            }
        })
    }

    // Send the user's data through an AJAX request, in order to save to the database
    save() {
        let save_data = JSON.stringify({
            'score': parseInt(this.scoreTarget.textContent),
            'upgrades': this.upgrades
        });

        $.ajax({
            url: '/game/save',
            method: 'POST',
            dataType: 'json',
            async: true,
            data: save_data,

            success: () => {
                this.outputTarget.textContent = 'Game saved';
                setTimeout(() => {
                        this.outputTarget.textContent = '';
                    }, 5000
                );
            },
            error: () => {
                this.outputTarget.textContent = 'Failed to save game';
                setTimeout(() => {
                        this.outputTarget.textContent = '';
                    }, 5000
                );
            }
        });
    }

    // After a click on the banana, increment the user's score
    add() {
        let score = this.scoreTarget.innerText;
        this.scoreTarget.innerText = ++score;
    }

    // Allows the user to buy an upgrade using his score, if they can afford it
    buy({ params: { name } }) {
        let score = parseInt(this.scoreTarget.innerText);
        let price = parseInt(this.upgrades[name]['price']);
        if (score >= price) {
            this.gain += parseInt(this.upgrades[name]['bps']);
            document.getElementById('value-' + name).innerHTML = ++this.upgrades[name]['amount'];
            score -= price
            this.scoreTarget.innerText = score
        }
    }
}
