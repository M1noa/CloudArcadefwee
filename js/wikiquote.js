"use strict";
/* random qoutes from WikiQoutes api */
var Wikiquote = (function() {

    var wqa = {};

    var API_URL = "https://en.wikiquote.org/w/api.php";

    /**
     * Query based on "titles" parameter and return page id.
     * If multiple page ids are returned, choose the first one.
     * Query includes "redirects" option to automatically traverse redirects.
     * All words will be capitalized as this generally yields more consistent results.
     */
    wqa.queryTitles = function(titles, success, error) {
        $.ajax({
            url: API_URL,
            dataType: "jsonp",
            data: {
                format: "json",
                action: "query",
                redirects: "",
                titles: titles
            },

            success: function(result, status) {
                var pages = result.query.pages;
                var pageId = -1;
                for(var p in pages) {
                    var page = pages[p];
                    // api can return invalid recrods, these are marked as "missing"
                    if(!("missing" in page)) {
                        pageId = page.pageid;
                        break;
                    }
                }
                if(pageId > 0) {
                    success(pageId);
                } else {
                    error("No results");
                }
            },

            error: function(xhr, result, status){
                error("Error processing your query");
            }
        });
    };

    /**
     * Get the sections for a given page.
     * This makes parsing for quotes more manageable.
     * Returns an array of all "1.x" sections as these usually contain the quotes.
     * If no 1.x sections exists, returns section 1. Returns the titles that were used
     * in case there is a redirect.
     */
    wqa.getSectionsForPage = function(pageId, success, error) {
        $.ajax({
            url: API_URL,
            dataType: "jsonp",
            data: {
                format: "json",
                action: "parse",
                prop: "sections",
                pageid: pageId
            },

            success: function(result, status){
                var sectionArray = [];
                var sections = result.parse.sections;
                for(var s in sections) {
                    var splitNum = sections[s].number.split('.');
                    if(splitNum.length > 1 && splitNum[0] === "1") {
                        sectionArray.push(sections[s].index);
                    }
                }
                // Use section 1 if there are no "1.x" sections
                if(sectionArray.length === 0) {
                    sectionArray.push("1");
                }
                success({ titles: result.parse.title, sections: sectionArray });
            },
            error: function(xhr, result, status){
                error("Error getting sections");
            }
        });
    };

    /**
     * Get all quotes for a given section.  Most sections will be of the format:
     * <h3> title </h3>
     * <ul>
     *   <li>
     *     Quote text
     *     <ul>
     *       <li> additional info on the quote </li>
     *     </ul>
     *   </li>
     * <ul>
     * <ul> next quote etc... </ul>
     *
     * The quote may or may not contain sections inside <b /> tags.
     *
     * For quotes with bold sections, only the bold part is returned for brevity
     * (usually the bold part is more well known).
     * Otherwise the entire text is returned.  Returns the titles that were used
     * in case there is a redirect.
     */
    wqa.getQuotesForSection = function(pageId, sectionIndex, success, error) {
        $.ajax({
            url: API_URL,
            dataType: "jsonp",
            data: {
                format: "json",
                action: "parse",
                noimages: "",
                pageid: pageId,
                section: sectionIndex
            },

            success: function(result, status){
                var quotes = result.parse.text["*"];
                var quoteArray = []

                // Find top level <li> only
                var $lis = $(quotes).find('li:not(li li)');
                $lis.each(function() {
                    // Remove all children that aren't <b>
                    $(this).children().remove(':not(b)');
                    var $bolds = $(this).find('b');

                    // If the section has bold text, use it.  Otherwise pull the plain text.
                    if($bolds.length > 0) {
                        quoteArray.push($bolds.html());
                    } else {
                        quoteArray.push($(this).html());
                    }
                });
                success({ titles: result.parse.title, quotes: quoteArray });
            },
            error: function(xhr, result, status){
                error("Error getting quotes");
            }
        });
    };

    /**
     * Search using opensearch api.  Returns an array of search results.
     */
    wqa.openSearch = function(titles, success, error) {
        $.ajax({
            url: API_URL,
            dataType: "jsonp",
            data: {
                format: "json",
                action: "opensearch",
                namespace: 0,
                suggest: "",
                search: titles
            },

            success: function(result, status){
                success(result[1]);
            },
            error: function(xhr, result, status){
                error("Error with opensearch for " + titles);
            }
        });
    };

    /**
     * Get a random quote for the given title search.
     * This function searches for a page id for the given title, chooses a random
     * section from the list of sections for the page, and then chooses a random
     * quote from that section.  Returns the titles that were used in case there
     * is a redirect.
     */
    wqa.getRandomQuote = function(titles, success, error) {

        var errorFunction = function(msg) {
            error(msg);
        };

        var chooseQuote = function(quotes) {
            var randomNum = Math.floor(Math.random()*quotes.quotes.length);
            success({ titles: quotes.titles, quote: quotes.quotes[randomNum] });
        };

        var getQuotes = function(pageId, sections) {
            var randomNum = Math.floor(Math.random()*sections.sections.length);
            wqa.getQuotesForSection(pageId, sections.sections[randomNum], chooseQuote, errorFunction);
        };

        var getSections = function(pageId) {
            wqa.getSectionsForPage(pageId, function(sections) { getQuotes(pageId, sections); }, errorFunction);
        };

        wqa.queryTitles(titles, getSections, errorFunction);
    };

    /**
     * Capitalize the first letter of each word
     */
    wqa.capitalizeString = function(input) {
        var inputArray = input.split(' ');
        var output = [];
        for(s in inputArray) {
            output.push(inputArray[s].charAt(0).toUpperCase() + inputArray[s].slice(1));
        }
        return output.join(' ');
    };

    return wqa;
}());
/* random qoutes from WikiQoutes api */
/* my script */
var authors = [
        "Mahatma Gandhi",
        "Albert Einstein",
        "Martin Luther King, Jr.",
        "Leonardo da Vinci",
        "Walt Disney",
        "Edgar Allan Poe",
        "Sigmund Freud",
        "Thomas A. Edison",
        "Robin Williams",
        "Steve Jobs"
    ],
    $quote = $('#quote'),
    $btns = $('.btn'),
    randInt = function(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    },
    randColor = function() {
        return 'rgb(' + randInt(0,128) + ',' + randInt(0,128) + ',' + randInt(0,128) + ')';
    },
    setHtml = function(q) {
       $('#quote').html(
           "<blockquote class='quote-text'>\""+ q.quote +"\"</blockquote>" +
            "<small class='author'> - "+ q.titles +"</small>"
       );
    },
    getQuote =  function() {
        $quote.css('opacity','0');
        Wikiquote.getRandomQuote(authors[randInt(0, 9)],
            function (quote) {
                console.log(quote.quote);
                if (quote.quote && quote.quote.length <= 180) {
                    setHtml(quote);
                    $quote.css('opacity','1');
                } else {
                    getQuote();
                }

            }, function (e) {
        });
    };