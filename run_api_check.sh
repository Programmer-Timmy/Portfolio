#!/bin/bash

cd /
cd "/c/Users/Timva/OneDrive - ROC Midden Nederland/Documenten/levels/level 4/code/portfolio-git"
php addvideos.php

if [ $? -eq 0 ]; then
  echo "API-controle succesvol uitgevoerd."
else
  echo "Fout bij het uitvoeren van de API-controle."
fi

chmod +x run_api_check.sh

crontab -e

0 0 * * * "c:/Users/Timva/OneDrive - ROC Midden Nederland/Documenten/levels/level 4/code/portfolio-git/run_api_check.sh"
