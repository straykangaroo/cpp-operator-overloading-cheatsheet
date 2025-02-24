# C++ Operator Overloading Cheatsheet


## Table of contents
1.  [Introduction](#introduction)
2.  [Brief recap](#brief-recap-what-is-operator-overloading)
3.  [Conventions](#conventions-used-in-this-guide)
4.  [Operators](#operators)
    1.  [Object access operators](#object-access-operators)
    2.  [Arithmetic operators](#arithmetic-operators)
    3.  [Bitwise operators](#bitwise-operators)
    4.  [Boolean (logical) operators](#boolean-logical-operators)
    5.  [Comparison (relational) operators](#comparison-relational-operators)
    6.  [Increment / Decrement operators](#increment--decrement-operators)
    7.  [I/O streams operators](#io-streams-operators)
    8.  [Other operators](#other-operators)
5.  [Contact](#contact)
6.  [Copyright notice](#copyright-notice)


## Introduction

The most challenging step in using _C++ operator overloading_ (and in using C++ in general, actually) is probably moving from theory to practice: the theory in textbooks might be clear but its practical application is often arduous.

C++ software developement in practice requires applying a fair deal of conventions, best practices, and professional knowledge whose content is abundant but often poorly digestible, as it is scattered in multiple textbooks, blogs, wikis, Q&A sites.

This document aims at bringing together in a sensible format all the knowledge needed to quickly and effectively fielding and making the most of C++ operator overloading, steering clear, above all, of the many _"How could I possibly know that?"_ traps.


## Brief recap: what is operator overloading?
Operator overloading is a kind of polymorphism, available in several programming languages, that allows the developer to define or redefine the behavior of the language operators (e.g. `+`, `*`, `<<`, etc.) for classes and (though discouraged) for primitive data types (e.g. `int`, `double`, etc.).

Though sometimes belittled as mere _syntactic sugar_ adding nothing to the language expressive power, it is indeed, when properly deployed, an extremely tasteful and energizing sugar, great for closing the gap between the source code and the domain model it is supposed to manipulate.

Short example of operating on hypothetical objects representing 2D euclidean vectors, *without* operator overloading:

```cpp
class Vector2D {
    // ...
};

Vector2D v1 = // ...
Vector2D v2 = // ...

Vector2D v3 = v1.add(v2).multiply(0.5);

if( ! v1.equal(v3) ) {
    v1 = v2.opposite();
}
```

... and *with* operator overloading:

```cpp
class Vector2D {
    // ...
};
    
Vector2D v1 = // ...
Vector2D v2 = // ...

Vector2D v3 = (v1 + v2) * 0.5;

if( v1 != v3 ) {
    v1 = -v2;
}
```


## Conventions used in this guide
<dl>
    <dt><code>C</code></dt>
        <dd>some container-like class</dd>
    <dt><code>T</code></dt>
        <dd>some type, maybe contained in a container-like <code>C</code> class</dd>
    <td><dt><code>I</code></dt>
        <dd>some iterator/pointer-like class</dd>
    <dt><code>X</code>, <code>Y</code>, <code>Z</code></dt>
        <dd>some type</dd>
</dl>


## Operators

### Object access operators
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>array subscript (non-const)</td>
            <td><code>T &amp; C::operator[](std::size_t idx)</code></td>
            <td><strong>Must be member</strong></td>
            <td>
                <ul>
                <li>The parameter may be <code>std::size_t</code> or whatever makes sense (see: <a href="https://en.cppreference.com/w/cpp/container">associative containers</a>)</li>
                <li>Multiple overloads are allowed</li>
                <li>If <code>T</code> is a built-in type, return by value</li>
                <li>Since C++23 may have multiple parameters</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>array subscript (const)</td>
            <td><code>const T &amp; C::operator[](std::size_t idx) const</code></td>
            <td><strong>Must be member</strong></td>
            <td>
                <ul>
                <li>The parameter may be <code>std::size_t</code> or whatever makes sense (see: <a href="https://en.cppreference.com/w/cpp/container">associative containers</a>)</li>
                <li>Multiple overloads are allowed</li>
                <li>If <code>T</code> is a built-in type, return by value</li>
                <li>Since C++23 may have multiple paramters</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>array subscript (for pointer like objects such as random access iterators)</td>
            <td><code>I I::operator[](std::size_t idx) const</code></td>
            <td><strong>Must be member</strong></td>
            <td>
                <ul>
                <li>Equivalent to <code>*this + idx</code> (may implement as such)</li>
                <li><code>I</code> should be cheap to copy</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>dereference (non-const)</td>
            <td><code>Y &amp; X::operator*()</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                </ul>
            </td>
        </tr>
        <tr>
            <td>dereference</td>
            <td><code>const Y &amp; X::operator*() const</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>If <code>Y</code> is a built-in type, return by value</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>arrow (non-const)</td>
            <td><code>Y * X::operator-&gt;()</code></td>
            <td><strong>Must be member</strong></td>
            <td>
                <ul>
                <li>Must return a pointer or a proxy object (overloading <code>operator-&gt;()</code> itself). Note that chaining occurs</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>arrow (const)</td>
            <td><code>const Y * X::operator-&gt;() const</code></td>
            <td><strong>Must be member</strong></td>
            <td>
                <ul>
                <li>Must return a pointer or a proxy object (overloading <code>operator-&gt;()</code> itself). Note that chaining occurs</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>pointer to member</td>
            <td><code>Y &amp; C::operator-&gt;*(X x)</code></td>
            <td>May be member or not</td>
            <td>
                <ul>
                </ul>
            </td>
        </tr>
        <tr>
            <td>address-of</td>
            <td><code>Y * X::operator&()</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                </ul>
            </td>
        </tr>
    </tbody>
</table>

### Arithmetic operators
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>addition (compound)</td>
            <td><code>X &amp; X::operator+=(const X &amp; other)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Parameter may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads are allowed</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>subtraction (compound)</td>
            <td><code>X &amp; X::operator-=(const X &amp; other)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Parameter may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads are allowed</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>multiplication (compound)</td>
            <td><code>X &amp; X::operator*=(const X &amp; other)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Parameter may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads are allowed</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>division (compound)</td>
            <td><code>X &amp; X::operator/=(const X &amp; other)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Parameter may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads are allowed</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>modulus (compound)</td>
            <td><code>X &amp; X:operator%=(const X &amp; other)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Parameter may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads are allowed</li>
                </ul>
            </td>
        </tr>        
        <tr>
            <td>addition</td>
            <td><code>X operator+(X left, const X &amp; right)</code></td>
            <td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator+=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)</li>
                <li>May return something other than <code>X</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>subtraction</td>
            <td><code>X operator-(X left, const X &amp; right)</code></td>
            <td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator-=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)</li>
                <li>May return something other than <code>X</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>multiplication</td>
            <td><code>X operator*(X left, const X &amp; right)</code></td>
            <td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator*=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)</li>
                <li>May return something other than <code>X</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>division</td>
            <td><code>X operator/(X left, const X &amp; right)</code></td>
            <td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator/=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)</li>
                <li>May return something other than <code>X</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>modulus</td>
            <td><code>X operator%(X left, const X &amp; right)</code></td>
            <td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator%=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)</li>
                <li>May return something other than <code>X</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>unary minus</td>
            <td><code>X X::operator-() const</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                </ul>
            </td>
        </tr>
        <tr>
            <td>unary plus</td>
            <td><code>X X::operator+() const</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                </ul>
            </td>
        </tr>        
    </tbody>
</table>

#### Examples
```cpp
// addition
X operator+(X left, const X &amp; right)
{
    left += right;
    return left;
}

// multiplication, heterogeneous types
X operator*(X left, float right)
{
    left *= float;
    return left;
}

X operator*(float left, const X & right)
{
    return right * left;
}
```

### Bitwise operators
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>bitwise and (compound)</td>
            <td><code>X &amp; X::operator&amp;=(const X &amp; other)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Parameter may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads are allowed</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise or (compound)</td>
            <td><code>X &amp; X::operator|=(const X &amp; other)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Parameter may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads are allowed</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise xor (compound)</td>
            <td><code>X &amp; X::operator^=(const X &amp; other)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Parametre may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads are allowed</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise left shift (compound)</td>
            <td><code>X &amp; X::operator&lt;&lt;=(std::size_t n)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Parameter is usually <code>std::size_t</code> or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads are allowed</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise right shift (compound)</td>
            <td><code>X &amp; X::operator&gt;&gt;=(const X &amp; other)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Parameter is usually <code>std::size_t</code> or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads are allowed</li>
                </ul>
            </td>
        </tr>        
        <tr>
            <td>bitwise and</td>
            <td><code>X operator&amp;(X left, const X &amp; right)</code></td>
            <td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator&amp;=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)</li>
                <li>May return something other than <code>X</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise or</td>
            <td><code>X operator|(X left, const X &amp; right)</code></td>
            <td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator|=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)</li>
                <li>May return something other than <code>X</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise xor</td>
            <td><code>X operator^(X left, const X &amp; right)</code></td>
            <td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator^=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)</li>
                <li>May return something other than <code>X</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise left shift</td>
            <td><code>X operator&lt;&lt;(X left, std::size_t n)</code></td>
            <td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator&lt;&lt;=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li>Parameter <code>n</code> is usually <code>std::size_t</code> or whatever makes sense, but beware conversions</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise right shift</td>
            <td><code>Xoperator&gt;&gt;(X left, const X &amp; right)</code></td>
            <td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator&gt;&gt;=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li>Parameter <code>n</code> is usually <code>std::size_t</code> or whatever makes sense, but beware conversions</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise not</td>
            <td><code>X X::operator~() const</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                </ul>
            </td>
        </tr>
    </tbody>
</table>

#### Examples
```cpp
// logical and
X operator&(X left, const X & right)
{
    left &= right;
    return left;
}
```

### Boolean (logical) operators
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>logical and</td>
            <td><code>bool operator&amp;&amp;(const X &amp; left, const X &amp; right)</code></td>
            <td>Should be non member</td>
            <td>
                <ul>
                <li>If overloaded will not have short circuit semantics</li>
                <li>May also return <code>X</code> or some other type</li>
                <li>Until C++17 no sequence point holds</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>logical or</td>
            <td><code>bool operator||(const X &amp; left, const X &amp; right)</code></td>
            <td>Should be non member</td>
            <td>
                <ul>
                <li>If overloaded will not have short circuit semantics</li>
                <li>May also return <code>X</code> or some other type</li>
                <li>Until C++17 no sequence point holds</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>logical not</td>
            <td><code>bool X::operator!() const</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>May also return <code>X</code> or some other type</li>
                </ul>
            </td>
        </tr>
    </tbody>
</table>

### Comparison (relational) operators
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>equality</td>
            <td><code>bool operator==(const X &amp; left, const X &amp; right)</code></td>
            <td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>inequality</td>
            <td><code>bool operator!=(const X &amp; left, const X &amp; right)</code></td>
            <td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                <li>Implement in terms of <code>operator==</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>less-than</td>
            <td><code>bool operator&lt;(const X &amp; left, const X &amp; right)</code></td>
            <td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>less-or-equal-than</td>
            <td><code>bool operator&lt;=(const X &amp; left, const X &amp; right)</code></td>
            <td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                <li>Implement in terms of <code>operator&gt;</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>greater-than</td>
            <td><code>bool operator&gt;(const X &amp; left, const X &amp; right)</code></td>
            <td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                <li>Implement in terms of <code>operator&lt;</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>greater-than-or-equal</td>
            <td><code>bool operator&gt;=(const X &amp; left, const X &amp; right)</code></td>
            <td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                <li>Implement in terms of <code>operator&lt;=</code></li>
                </ul>
            </td>
        </tr>
    </tbody>
</table>

#### Examples
```cpp
// inequality
bool operator!=(const X & left, const X & right)
{
    return ! (left == right);
}

// less-or-equal-than
bool operator<=(const X & left, const X & right)
{
    return ! (left > right);
}

// greater-than
bool operator>(const X & left, const X & right)
{
    return right < left;
}

// greater-than-or-equal
bool operator>=(const X & left, const X & right)
{
    return right <= left;
}
```

### Increment / Decrement operators
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>pre-increment</td>
            <td><code>X &amp; X::operator++()</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>    
                </ul>
            </td>
        </tr>
        <tr>
            <td>post-increment</td>
            <td><code>X X::operator++(int)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Dummy <code>int</code> parameter is required</li>
                <li>Return "old" <code>*this</code></li>
                <li>Implement in terms of <code>operator++()</code></li>
            </ul>
            </td>
        </tr>
        <tr>
            <td>pre-decrement</td>
            <td><code>X &amp; X::operator--()</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>    
                </ul>
            </td>
        </tr>
        <tr>
            <td>post-decrement</td>
            <td><code>X X::operator--(int)</code></td>
            <td>Should be member</td>
            <td>
                <ul>
                <li>Dummy <code>int</code> parameter is required</li>
                <li>Return "old" <code>*this</code></li>
                <li>Implement in terms of <code>operator--()</code></li>
            </ul>
            </td>
        </tr>
    </tbody>
</table>

#### Examples
```cpp
// pre-increment
X & X::operator++()
{
    // DO INCREMENT HERE...
    return *this;
}

// post-increment
X X::operator++(int)
{
    X old{*this};
    ++*this;
    return old;
}
```

### I/O streams operators
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>stream extraction</td>
            <td><code>std::ostream &amp; operator&lt;&lt;(std::ostream &amp; os, const X &amp; x)</code></td>
            <td><strong>Must not be member</strong></td>
            <td>
                <ul>
                <li>Should return <code>os</code></li>
                <li>Restore stream state if modified</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>stream insertion</td>
            <td><code>std::istream &amp; operator&gt;&gt;(std::istream &amp; is, X &amp; x)</code></td>
            <td><strong>Must not be member</strong></td>
            <td>
                <ul>
                <li>Should return <code>is</code></li>
                <li>Set stream state if errors</li>
                </ul>
            </td>
        </tr>
    </tbody>
</table>

### Other operators
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>function call</td>
            <td><code>Z X::operator()(Y y) const</code></td>
            <td><strong>Must be member</strong></td>
            <td>
                <ul>
                <li>May be <code>const</code>, or not</li>
                <li>Return type and (multiple) parameters as needed</li>
                <li>Multiple overloads are allowed</li>
                <li>Tangentially related: function objects should be cheap to copy</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>comma</td>
            <td><code>Y operator,(const X &amp; left, const Y &amp; right)</code></td>
            <td>Should be non-member</td>
            <td>
                <ul>
                <li>May return whatever makes sense</li>
                <li>Beware: no sequence point holds, so operands may be evaluated in any order</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>conversion</td>
            <td><code>X::operator Y() const</code></td>
            <td>May be member or not</td>
            <td>
                <ul>
                <li>Since C++ 11 may be marked <code>explicit</code></li>
                <li>Return type will be <code>Y</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>copy assignment</td>
            <td><code>X &amp; X::operator=(const X &amp; other)</code></td>
            <td><strong>Must be member</strong></td>
            <td>
                <ul>
                    <li>Return <code>*this</code></li>
                    <li>Should free the resources held by <code>*this</code></li>
                    <li>Should make a deep copy of the resources held by <code>other</code></li> 
                    <li>Might be <i>defaulted</i> or <i>deleted</i> (since C++11)</li> 
                </ul>
            </td>
        </tr>
        <tr>
            <td>move assignment</td>
            <td><code>X &amp; X:::operator=(X &amp;&amp; other)</code></td>
            <td><strong>Must be member</strong></td>
            <td>
                <ul>
                    <li>Return <code>*this</code></li>
                    <li>Should free the resources held by <code>*this</code></li>
                    <li>Should "steal" the resources held by <code>other</code> and pass them to <code>*this</code></li>
                    <li>Should leave <code>other</code> in a "null-like" but destructible state</li>
                    <li>Might be <i>defaulted</i> or <i>deleted</i> (since C++11)</li>
                    <li>Should be <code>noexcept</code></li>
                </ul>
            </td>
        </tr>
    </tbody>
</table>


## Contact

[https://github.com/straykangaroo/cpp-operator-overloading-cheatsheet](https://github.com/straykangaroo/cpp-operator-overloading-cheatsheet)


## Copyright notice
&copy; 2023 Costantino Astithas
