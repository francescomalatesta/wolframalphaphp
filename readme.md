# Wolfram|Alpha PHP Wrapper

This is a PHP Wrapper for [Wolfram|Alpha](http://wolframalpha.com) APIs (V2), the computational knowledge engine made by Wolfram Research.

I made it because the one on the official website was missing some parts and methods that, in my opinion, I find necessary and useful.

In this readme file I will explain how to setup and use it in your PHP project.

## Introduction

In order to work with Wolfram|Alpha Engine API, you must have an AppID. To have an AppID, you must be [registered as a developer on the Wolfram|Alpha Developer Portal](https://developer.wolframalpha.com/portal/signin.html). It is a really simple procedure: when registered, all you will have to do is to create a new application, specifying a name and a description.

Actually, you can have 2000 monthly API free calls. If you don't like this, [get in touch with Wolfram](http://products.wolframalpha.com/api/) to find a solution for your needs.

## How to Install the Wrapper

Installing the Wolfram|Alpha PHP Wrapper is very easy. In your project, just add this to your **require** element in _composer.json_. 

    {
        "require": {
            "francescomalatesta/wolframalpha": "dev-master"
        }
    }

If you don't know what Composer is, [it's time to do something about it](https://getcomposer.org/)! :) 

Just a _composer install_ and your done: now let's move to the Engine class.

## The Engine

The _Engine_ class is your access point to send your queries to the Wolfram|Alpha Engine. You can initialize it with this simple syntax.

    $engine = new WolframAlpha\Engine('YOUR_APP_ID');

Nothing more.

### process()

You can send a query to the engine with this method. Here's the signature.

    public function process($query, $assumptions = array(), $formats = array('image', 'plaintext'))

In _$query_ you will have to put the query you want to send (for example: ["who is buying a stairway to heaven?"](http://www.wolframalpha.com/input/?i=who+is+buying+a+stairway+to+heaven%3F)).

In _$assumptions_ you will be able to specify one or more assumptions to apply to the query. If you don't know them check out the [API Documentation](http://products.wolframalpha.com/api/documentation.html#6) and the end of this readme.

In _$formats_ you will put the desired format for the request output. By default you have _image_ and _plaintext_. You can also use: _html_, _cell_, _sound_ and _minput_.

Here you can see a really simple call example.

    // sending a query with the 'e' character
    $result = $engine->process('e');

That's all.

Sometimes a query can take a long time to execute, so you could find yourself comfortable to validate the query before make the request. The _Engine_ class has a _validate()_ method that will help you.

Here's the signature.

    public function validate($query, $assumptions = array())

**Important:** the _process()_ method returns a _QueryResult_ object. The _validate()_ method returns a _ValidateQueryResult_ object. Continue reading to know more about. 

**Important 2:** the output data format from Wolfram|Alpha Engine is XML. I used PHP's SimpleXML to work with XML nodes. If you need to work with XML too, every class (except for the _Image_ class) has the _parsedXml_ property, a _SimpleXMLElement_ instance. This means that, if you want, you can work with XML everywhere, with or without our classes.

    // output echo
    echo $result->parsedXml->asXML();
    
    // saving XML output on a file
    $result->parsedXML->asXML($fileName);

## The QueryResult

The _QueryResult_ class contains everything you need to work with your response. First of all, let's take a look the the basics methods.

### Attributes

You can access to every single query result attribute just specifying it as a property.

    var_dump($result->success);

I used magic methods to add a more simple and useful syntax. You can find every single attribute on the [reference page of the documentation](http://products.wolframalpha.com/api/documentation.html#8).

### Useful Methods

It's not just about attributes, however. I added some useful methods to control warnings, errors and so on.

#### Errors and Warnings

In Wolfram|Alpha you can have many kinds of issues in your output. As you can easily imagine, a **warning** is different than an **error**.

In fact, a Warning does not break the procedure. An error does.

With the _hasWarnings()_ and _getWarnings()_ you can deal with warnings in your code. Here's an example.

    if($result->hasWarnings())
    {
        foreach($result->getWarnings() as $name => $message)
        {
            echo $name: . ' ' . $message;
        }
    }

The _hasWarnings()_ methods returns a simple boolean value. The _getWarnings()_ method returns an associative array with the $name => $message format.

#### getError()

The same you just saw for warnings goes for errors, but with a little difference... in a Wolfram|Alpha Engine request an error is always just one, in a single query.

Here's an example. The syntax should be quite easy to understand.

    if($result->hasError())
    {
        echo 'Error ' . $result->getError()['code'] . ': ' . $result->getError()['message'];
    }

#### hasProblems()

Another "check" method is _hasProblems()_ it's used just to control the status of the _success_ attribute of the _queryresult_ XML item. It returns a boolean.

    if($result->hasProblems())
    {
        // deal with it...
    }

#### Tips and Suggestions

You know, the Wolfram|Alpha Engine is a really good, good guy. It does not only some dirty maths and processing for you, but also sometimes suggests you what you can mean if you write something wrong.

**Tips** are used if you typed something wrong and the Engine didn't understand it. _getTips()_ is a method that returns an array of strings that you can use like this:

    if(count($result->getTips()) > 0)
    {
        foreach($result->getTips() as $tip)
        {
            echo 'Tip: ' . $tip;
        }
    }

**Suggestions** are a little more specific. If a tip gives you an advice for using the Engine in a right way, a suggestion is something like "oh, you wrote Chucago, but I think you might intend Chicago!"

You can check out for suggestions with the _getSuggestions()_ method.

    if(count($result->getSuggestions()) > 0)
    {
        foreach($result->getSuggestions() as $suggestion)
        {
            echo 'Suggestion: ' . $suggestion;
        }
    }

## Pods and Subpods

In the Wolfram|Alpha world, results are organized in Pods. You can see them as "elements" that makes up the final result in its complexity. Every Pod, also, has one or more Subpods. A Subpod is the single "unit" of the result. It can be a text, or an image (and so on).

With the Wrapper, reading results of your request is very easy.

First of all, you can access your Pods with the _pods_ property.

    $pods = $result->pods;

**Note:** if the query hasn't pods, _$result->pods_ will be _null_. Check it with _isset()_, if needed.

_$pods_ is an instance of _PodsCollection_ class. It's a special object, as you can treat it like an array (thanks to the _ArrayAccess_ and _Countable_ interfaces) but also has other methods.

Let's make an example:

    // counting result pods
    echo count($result->pods);
    
    // access a specific Pod
    var_dump($result->pods['Input']);
    
However, sometimes is not so useful to iterate in the array until you find the Pod you need. The _PodsCollection_ class has also some utility methods to have a more expressive code. These methods are _has()_ and _find()_.

    if($pods->has('Input'))
    {
        $inputPod = $pods->find('input');
    }

As you can imagine, _has()_ takes the id of the desired Pod as an input and returns _true_ if the Pod exists, _false_ otherwise. _find()_ takes the same parameter as input and returns an instance of the _Pod_ class if exists, _null_ otherwise.

Talking about Pods, however, a _PodsCollection_ is a collection of _Pod_ instances.

### Pod Class

The _Pod_ class is an abstraction of the result Pod that you can easily use in your application. You can access every Pod attribute as a property:

    echo $pod->title;
    echo $pod->id;
    echo $pod->position;

... and so on.

You can access your Subpods with:

    $pod->subpods;

_subpods_ is a simple array of _Subpod_ objects.

### Subpod Class

A Pod has its own Subpods. A Subpod has its own values.

You can access to your Subpod values like this:

    // access to a simple element
    echo $subpod->plaintext;

Also, you could have an image as a result in a subpod. In this case, the image data will be wrapped in an _Image_ object.

    $image = $subpod->img;
    
    echo $image->src;

Available properties for an image are: _src_, _width_, _height_, _title_ and _alt_.

**Tip:** when you want to work with a Subpod value but you're not sure about its existence, use a simple

    if(isset($subpod->plaintext))
    {
        // ready to go!
    }

## Assumptions

Assumptions are the way Wolfram|Alpha tells you "hey, I have some results but what you asked me means many different things!". It is a different concept from "suggestions", remember.

As happened for Pods, you can access (if they exists) assumptions for your query with:

    $assumptions = $result->assumptions;

**Note:** if the query hasn't assumptions, _$result->assumptions_ will be _null_. Check it with _isset()_, if needed.

The _$result->assumptions_ will be an instance of _AssumptionsCollection_ class. Same thing for the _PodsCollection_ class: you can iterate in it as an array and count its elements with a simple _count()_.

    foreach($assumptions as $type => $assumption)
    {
        echo 'Type: ' . $type;
        
        // deal with assumptions here...
    }

You can also use _has()_ and _find()_ methods, with the same flavour you saw before in _PodsCollection_.

    if($assumptions->has('Clash'))
    {
        $assumption = $assumptions->find('Clash');
        
        // your awesome code goes here
    }

Every element in the _AssumptionsCollection_ will be an instance of the _Assumption_ class.

### Assumption and AssumptionValue

Dealing with assumptions is quite easy with these classes. First of all, you can access the assumption attributes as object properties:

    echo $assumption->type;

In the same way a Pod has its own Subpods, an Assumption has its own Assumption values. You can find them in the _$assumption->values_ as an associative array of _AssumptionValue_ instances, where the key is the name of the value.

Same thing for attributes access, and here's an example:

    $assumptionValue = $assumption->values['Character'];
    
    echo $assumptionValue->desc;
    // outputs: a character
    
    echo $assumptionValue->input;
    // outputs: *C.eta-_*Character-

**Note:** once you have the assumption value input, you can apply to a new request specifying the string contained in _$assumptionValue->input_ as an element of the _$assumptions_ array in the _process()_ method of the _Engine_ class.

## The ValidateQueryResult

Sometimes, a complete process with the Wolfram|Alpha Engine can take a lot of time. You may find useful to know that, if you want, you can just validate a query instead of giving it to the engine without know what will happen.

The _Engine_ class has a _validate_ method that will solve the problem. It is similar to the _process()_ method, as you saw before, just without some parameters. You can specify some assumptions, if you want.

The _validate()_ method will return a _ValidateQueryResult_ instance, a little bit lighter than a _QueryResult_ object as it will contain only warnings, error data and assumptions.

**Note:** using the _validate()_ method it's not about saving some API calls but processing time. So, think that if you want to validate a query and then process it, it will cost you two API calls.

## Other Notes

Here we are at the end! This is my first "serious" project here on Github. So, for every feedback, or bug, or whatever you think is interesting, get in touch with me.

In the "tests" folder you can find... tests, of course. However, bugs always happens. _Issues_ is your friend!

If you want a direct contact, feel free to find me on twitter at [@malatestafra](https://twitter.com/malatestafra), or maybe [add me on Facebook](https://www.facebook.com/malatesta.francesco). If you don't like social networks, an email to francescomalatesta(at)live(dot)it will be fine :)
