import {Controller} from "@hotwired/stimulus";
import $ from "jquery";

export default class extends Controller {
    static targets = ['score', 'upgrade', 'output'];

    save() {
        let score = parseInt(this.scoreTarget.textContent);
        let upgrades = {};
        this.upgradeTargets.forEach((element) => {
            let name = element.getElementsByClassName("name")[0].innerText;
            upgrades[name] = parseInt(element.getElementsByClassName("value")[0].innerText);
        })

        let save_data = {
            'score': score,
            'upgrades': upgrades
        }

        let json_save_data = JSON.stringify(save_data)

        $.ajax({
            url: '/game',
            type: 'POST',
            dataType: 'json',
            async: true,
            data: json_save_data,

            success: () => {
                this.outputTarget.textContent = 'Game saved';
                setTimeout(
                    () => {
                        this.outputTarget.textContent = '';
                    },
                    5000
                );
            },
            error: () => {
                this.outputTarget.textContent = 'Failed to save game';
                setTimeout(
                    () => {
                        this.outputTarget.textContent = '';
                    },
                    5000
                );
            }
        })
    }
}