{*
* 2007-2017 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


<div id="newsletter_block_left" class="email_subscription block">
  <h4>{l s='Stay up to date' d='Modules.Emailsubscription.Shop'}</h4>
  <div class="block_content">
  {if $msg}
    <p class="{if $nw_error}warning_inline{else}success_inline{/if}">{$msg}</p>
  {/if}
  <form action="{$urls.pages.index}" method="post" class="newsletter-form relative">
    <input id="newsletter-input" type="email" name="email" placeholder="{l s='Your e-mail' d='Modules.Emailsubscription.Shop'}" value="{if isset($value) && $value}{$value}{/if}" required />
    {if $conditions}
      <p>{$conditions}</p>
    {/if}
    <div class="RodoForm hiddenForm">
					<div class="RodoFormGroup">
						<div class="checker"><span><input type="checkbox" name="Rodo1" required=""></span></div>
						<label for="Rodo1" class="col-sm-11">Wyrażam zgodę na otrzymywanie drogą elektroniczną na wskazany przeze mnie
							adres e-mail informacji handlowej w rozumieniu art. 10 ust. 1 ustawy z dnia 18
							lipca 2002 roku o świadczeniu usług drogą elektroniczną od ERgold Eryk Łapiński. o NIPie 966-139-59-39 ; mail : biuro@ergold.pl. Oświadczam, że zostałam(em)
							poinformowana(y) o przysługującym mi prawie dostępu do treści moich danych
							osobowych oraz ich poprawiania oraz żądania usunięcia, jak również prawa do
							wycofania zgody w każdym czasie.
						</label>
					</div>
				<div class="RodoFormGroup">
						<div class="checker"><span><input type="checkbox" name="Rodo2" required=""></span></div>
						<label for="Rodo2" class="col-sm-11">Wyrażam zgodę na przetwarzanie moich danych osobowych w rozumieniu
							rozporządzenia RODO o ochronie danych osobowych oraz ustawy z dnia 16 lipca
							2004 roku Prawo telekomunikacyjne w celach marketingu bezpośredniego
							przez ERgold Eryk Łapiński o NIPie 966-139-59-39 ; mail : biuro@ergold.pl i

							oświadczam, iż podanie przeze mnie danych osobowych jest dobrowolne.
							Oświadczam, że zostałam(em) poinformowana(y) o przysługującym mi prawie
							dostępu do treści moich danych osobowych oraz ich poprawiania oraz żądania ich
							usunięcia, jak również prawie do wycofania zgody w każdym czasie.
						</label>
				</div>
				<div class="RodoFormGroup">
						<div class="checker"><span><input type="checkbox" name="Rodo3" required=""></span></div>
						<label for="Rodo3" class="col-sm-11">Wyrażam zgodę na używanie telekomunikacyjnych urządzeń końcowych do
							marketingu bezpośredniego przez ERgold Eryk Łapiński o NIPie 966-139-59-39 ;
							mail : biuro@ergold.pl, że zostałem poinformowany o prawie do wycofania zgody w
							każdym czasie.
						</label>
				</div>
				</div>
    <input type="submit" value="Zapisz się" class="btn submitNewsletter" name="submitNewsletter" />
    <input type="hidden" name="action" value="0" />
  </form>
  </div>
</div>
