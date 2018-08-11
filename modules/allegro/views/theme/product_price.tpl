{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, 2)}
{convertPrice price=$productPrice}