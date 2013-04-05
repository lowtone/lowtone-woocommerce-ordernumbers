��          �      \      �  x  �  7  J  /   �     �  �   �     �     �  �  �     D  D   Y  3   �  G   �  F   	  E   a	  G   �	  C   �	  "   3
  '   V
     ~
  �  �
    Q  �  l  6   +     b    j     }     �  "  �      �  H   �  6   *  R   a  W   �  T     D   a  \   �  0     +   4     `                       	                        
                                                    <strong>Padding</strong> &mdash; Padding could be used to extend the value to a default length. For instance when using the order_count value padding could be used to create an four-digit number even for the lower numbers by adding some zeros to the left of the number (e.g. "1" becomes "0001"). Padding is defined by two values separated by a comma, the first value being the desired length, the optional second value the character used for the padding which defaults to "0". When the given length is less then the length of the value characters will be removed from the left side of the value until it matches the required length. <strong>Split</strong> &mdash; The second modifier could be used to make the order number more readable. Like the padding it consists of two values separated by a comma, the first being the size for each part and the second optional value being a character used to glue the parts together which defaults to "-". A template used for creating new order numbers. Key Modifiers can be added behind the key to convert the value to a default format. Modifiers are separated from the key and each other by a colon. Currenty two modifiers are available, namely (in this order): Order number format Order numbers Provide a template for formatting new order numbers. Tags can be used to add dynamic values to the order number. A tag consists of a key surrounded by percent signs like %year%. The key refers to a value that will replace the tag, the previous example for instance would add the year in which the order was placed to the order number. Below is a list of available tags (additional tags could be added by plugins). The ID of the order. The ISO-8601 week number for the year in which the order was placed. The day of the month in which the order was placed. The number of orders placed in the month in which the order was placed. The number of orders placed in the year in which the order was placed. The number of orders placed on the day on which the order was placed. The numeric representation for the month in which the order was placed. The total number of orders placed by the user who placed the order. The total number of orders placed. The year in which the order was placed. Value Project-Id-Version: WooCommerce Order Numbers
POT-Creation-Date: 2013-01-10 18:49+0100
PO-Revision-Date: 2013-01-31 17:48+0100
Last-Translator: Paul van der Meijs <info@lowtone.nl>
Language-Team: Lowtone <info@lowtone.nl>
Language: Dutch
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
X-Generator: Poedit 1.5.4
X-Poedit-KeywordsList: __
X-Poedit-Basepath: .
X-Poedit-SourceCharset: UTF-8
X-Poedit-SearchPath-0: ../..
 <strong>Padding</strong> &mdash; Padding kan gebruikt worden om een waarde een vaste lengte te geven. Wanneer bijvoorbeeld van de order_count waarde gebruik gemaakt wordt kan er van padding gebruik gemaakt worden om, ook voor lagere aantallen, een viercijferig getal te krijgen door nullen aan de linkerkant van het cijfer toe te voegen ("1" wordt zo bijvoorbeeld "0001"). Padding wordt gedefinieerd door twee waarden die van elkaar gescheiden zijn met een komma, de eerste van deze waarden is de gewenste lengte, de optionele tweede waarde is het teken dat gebruikt wordt om de waarde aan te vullen. Standaard is dit "0". Wanneer de gewenste lengte korter is dan de lengte van de betreffende waarde zullen er tekens aan de linkerkant afgehaald worden totdat de waarde de gewenste lengte heeft. <strong>Splitsen</strong> &mdash; De tweede modifier waar gebruik van gemaakt kan worden is bedoeld om het ordernummer beter leesbaar te maken. Net als bij padding bestaat deze uit twee waarden, van elkaar gescheiden met een komma, waarvan de eerste waarde de lengte aangeeft voor de stukken waarin de waarde opgesplitst wordt en de tweede, optionele waarde een teken definieert waarmee de stukken aan elkaar geplakt worden. Standaard is dit "-". Een sjabloon voor het opmaken van nieuwe ordernummers. Sleutel Modifiers kunnen achter de sleutel toegevoegd worden om de waarde om te zetten naar een standaard formaat. Modifiers zijn van elkaar en van de sleutel gescheiden door middel van een dubbele punt. Er zijn op dit moment twee modifiers beschikbaar, dit zijn (in deze volgorde): Ordernummer opmaak Ordernummers Geef een sjabloon op voor de opmaak van de ordernummers. Hierbij kan gebruik gemaakt worden van tags om dynamische waarden aan het ordernummer toe te voegen. Een tag bestaat uit een sleutel ingesloten door procent-tekens, bijvoorbeeld %year%. De sleutelwaarde refereert naar een waarde waardoor de tag vervangen zal worden, in het vorige voorbeeld zal bijvoorbeeld het jaar waarin de bestelling geplaatst is aan het ordernummer toegevoegd worden. Hieronder vind je een lijst van beschikbare tags (meer tags kunnen door plugins toegevoegd worden). Het ID nummer van de bestelling. Een ISO-8601 weeknummer voor het jaar waarin de bestelling is geplaatst. De dag van de maand waarin de bestelling is geplaatst. Het aantal bestellingen dat geplaatst is in de maand waarin de order is geplaatst. Het aantal bestellingen dat geplaatst is in het jaar waarin de bestelling is geplaatst. Het aantal bestellingen dat is geplaatst op de dag waarop de bestelling is geplaatst De numerieke waarde voor de maand waarin de bestelling is geplaatst. Het totaal aantal bestellingen dat door de gebruiker die de bestelling plaatst geplaatst is. Het totaal aantal bestellingen dat geplaatst is. Het jaar waarin de bestelling is geplaatst. Waarde 