<body>

<h1>C++ Operator Overloading Cheatsheet</h1>
<p>
The most challenging step in using <i>C++ operator overloading</i> (and in using C++ in general, actually) is probably moving from theory to practice. The theory in textbooks might be clear but its practical application is often arduous.
</p>
<p>
C++ software developement in practice requires applying a fair deal of conventions, best practices, and professional knowledge whose content is abundant but often scantly digestible, as it is scattered in multiple textbooks, blogs, wikis, Q&amp;A sites.
</p>
<p>
This document aims at bringing together in a sensible format all the knowledge needed to quickly and effectively fielding and making the most of C++ operator overloading, steering clear, above all, of the many <i>"How could I possibly know?"</i> traps.
</p>

<nav>
    <h2>Table of contents</h2>
    <ol>
        <li><a href="#access">Object access operators</a></li>
        <li><a href="#arithmetic">Arithmetic operators</a></li>
        <li><a href="#bitwise">Bitwise operators</a></li>
        <li><a href="#boolean">Boolean (logical) operators</a></li>
        <li><a href="#comparison">Comparison (relational) operators</a></li>
        <li><a href="#inc_dec">Increment / Decrement operators</a></li>
        <li><a href="#i_o">I/O streams operators</a></li>
        <li><a href="#memory">Memory management operators</a></li>
        <li><a href="#other">Other operators</a></li>
    </ol>
</nav>

<h2>Brief recap: what is operator overloading?</h2>
<p>
Operator overloading is a kind of polymorphism, available in several programming languages, that allows the developer to define or redefine the behavior of the language operators (e.g. <code>+</code>, <code>*</code>, <code>&lt;&lt;</code>, etc.) for classes and (though discouraged) for primitive data types (e.g. <code>int</code>, <code>double</code>, etc.).
</p>
<p>
Though sometimes belittled as mere <i>syntactic sugar</i> adding nothing to the language expressive power, it is indeed, when properly deployed, an extremely tasteful and energizing sugar, great for closing the gap between the source code and the domain model it is supposed to manipulate.
</p>
<p>
Short example of operating on hypothetical objects representing 2D euclidean vectors, <em>without</em> operator overloading
</p>
<pre><code>
    class Vector2D {
        /* ... */
    };
    
    Vector2D v1 = /* ...*/;
    Vector2D v2 = /* ...*/;
    
    Vector2D v3 = v1.add(v2).multiply(0.5);

    if( ! v1.equal(v3) ) {
        v1 = v2.opposite();
    }
</code></pre>
<p>... and <em>with</em> operator overloading</p>
<pre><code>
class Vector2D {
    /* ... */
};

Vector2D v1 = /* ...*/;
Vector2D v2 = /* ...*/;

Vector2D v3 = (v1 + v2) * 0.5;

if( v1 != v3 ) {
    v1 = -v2;
}
</code></pre>


<h2>Conventions used in this guide</h2>
<dl>
    <dt><code>C</code>, <code>X</code>, <code>Y</code></dt><dd>some types</dd>
    <dt><code>T</code></dt><dd>some type, maybe contained in a container-like <code>C</code> class</dd>
</dl>

<h2>Operators</h2>

<h3 id="access">Object access operators</h3>
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>array subscript (non-const)</td><td><code>T &amp; C::operator[](std::size_t idx)</code></td><td>Must be member</td>
            <td>
                <ul>
                <li><code>std::size_t</code> or whatever makes sense (see: associative containers)</li>
                <li>Multiple overloads possible</li>
                <li>If <code>T</code> is a built-in type, return by value</li>
                <li>Since C++23 may have multiple params</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>array subscript (const)</td><td><code>const T &amp; C::operator[](std::size_t idx) const</code></td><td>Must be member</td>
            <td>
                <ul>
                <li><code>std::size_t</code> or whatever makes sense (see: associative containers)</li>
                <li>Multiple overloads possible</li>
                <li>If <code>T</code> is a built-in type, return by value</li>
                <li>Since C++23 may have multiple params</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>array subscript (for pointer like objects e.g. random access iterators)</td><td><code>C C::operator[](std::size_t idx) const</code></td><td>Must be member</td>
            <td>
                <ul>
                <li>Equivalent to <code>*this + idx</code> (may implement as such)</li>
                <li><code>C</code> should be cheap to copy</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>dereference (non-const)</td><td><code>T &amp; operator*()</code></td><td><mark>Must be member</mark></td>
            <td>
                <ul>
                <li>If <code>T</code> is a built-in type, return by value</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>dereference</td><td><code>const T &amp; operator*() const</code></td><td><mark>Must be member</mark></td>
            <td>
                <ul>
                <li>If <code>T</code> is a built-in type, return by value</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>arrow (non-const)</td><td><code>T * operator-&gt;()</code></td><td>Must be member</td>
            <td>
                <ul>
                <li>Must return a pointer or a proxy object (overloading <code>operator-&gt;()</code> itself). Note that chaining occurs.</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>arrow (const)</td><td><code>const T * operator-&gt;() const</code></td><td>Must be member</td>
            <td>
                <ul>
                <li>Must return a pointer or a proxy object (overloading <code>operator-&gt;()</code> itself). Note that chaining occurs.</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>pointer to member of pointer</td><td><code>Y &amp; C::operator-&gt;*(X x);</code></td><td>May be member or not</td>
            <td>
                <ul>
                </ul>
            </td>
        </tr>
    </tbody>
</table>

<h3 id="arithmetic">Arithmetic operators</h3>
    <table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>addition (compound)</td><td><code>C &amp; C::operator+=(const C &amp; other)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Param may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads possible</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>subtraction (compound)</td><td><code>C &amp; C::operator-=(const C &amp; other)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Param may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads possible</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>multiplication (compound)</td><td><code>C &amp; C::operator*=(const C &amp; other)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Param may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads possible</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>division (compound)</td><td><code>C &amp; C::operator/=(const C &amp; other)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Param may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads possible</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>modulus (compound)</td><td><code>C &amp; C::operator%=(const C &amp; other)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Param may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads possible</li>
                </ul>
            </td>
        </tr>        
        <tr>
            <td>addition</td><td><code>C operator+(C left, const C &amp; right)</code></td><td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator+=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)
                </li><li>May return something other than <code>C</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>subtraction</td><td><code>C operator-(C left, const C &amp; right)</code></td><td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator-=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)
                </li><li>May return something other than <code>C</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>multiplication</td><td><code>C operator*(C left, const C &amp; right)</code></td><td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator*=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)
                </li><li>May return something other than <code>C</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>division</td><td><code>C operator/(C left, const C &amp; right)</code></td><td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator/=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)
                </li><li>May return something other than <code>C</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>modulus</td><td><code>C operator%(C left, const C &amp; right)</code></td><td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator%=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)
                </li><li>May return something other than <code>C</code></li></ul>
            </td>
        </tr>
        <tr>
            <td>unary minus</td><td><code>C C::operator-() const</code></td><td>Should be member</td>
            <td>
                <ul>
                </ul>
            </td>
        </tr>
        <tr>
            <td>unary plus</td><td><code>C C::operator+() const</code></td><td>Should be member</td>
            <td>
                <ul>
                </ul>
            </td>
        </tr>        
    </tbody>
</table>
<h4>Examples</h4>
<pre><code>
// addition
C operator+(C left, const C &amp; right)
{
    left += right;
    return left;
}

// multiplication, heterogeneous types
C operator*(C left, float right)
{
    left *= float;
    return left;
}

C operator*(float left, const C &amp; right)
{
    return right * left;
}
</code></pre>

<h3 id="bitwise">Bitwise operators</h3>
    <table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>bitwise and (compound)</td><td><code>C &amp; C::operator&amp;=(const C &amp; other)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Param may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads possible</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise or (compound)</td><td><code>C &amp; C::operator|=(const C &amp; other)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Param may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads possible</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise xor (compound)</td><td><code>C &amp; C::operator^=(const C &amp; other)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Param may be the same type or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads possible</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise left shift (compound)</td><td><code>C &amp; C::operator&lt;&lt;=(std::size_t n)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Param usually <code>std::size_t</code> or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads possible</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise right shift (compound)</td><td><code>C &amp; C::operator&gt;&gt;=(const C &amp; other)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>
                <li>Param usually <code>std::size_t</code> or whatever makes sense, but beware conversions</li>
                <li>Multiple overloads possible</li>
                </ul>
            </td>
        </tr>        
        <tr>
            <td>bitwise and</td><td><code>C operator&amp;(C left, const C &amp; right)</code></td><td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator&amp;=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)
                </li><li>May return something other than <code>C</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise or</td><td><code>C operator|(C left, const C &amp; right)</code></td><td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator|=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)
                </li><li>May return something other than <code>C</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise xor</td><td><code>C operator^(C left, const C &amp; right)</code></td><td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator^=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li><code>left</code> and <code>right</code> may be of heterogeneous types (beware symmetry issues)
                </li><li>May return something other than <code>C</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise left shift</td><td><code>C operator&lt;&lt;(C left, std::size_t n)</code></td><td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator&lt;&lt;=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li>Param <code>n</code> usually <code>std::size_t</code> or whatever makes sense, but beware conversions</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise right shift</td><td><code>C operator&gt;&gt;(C left, const C &amp; right)</code></td><td>Should be non-member</td>
            <td>
                <ul>
                <li>Implement in terms of <code>operator&gt;&gt;=</code></li>
                <li>Note that <code>left</code> is passed by value</li>
                <li>Param <code>n</code> usually <code>std::size_t</code> or whatever makes sense, but beware conversions</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>bitwise not</td><td><code>C C::operator~() const</code></td><td>Should be member</td>
            <td>
                <ul>
                </ul>
            </td>
        </tr>
    </tbody>
</table>
<h4>Examples</h4>
<pre><code>
// logical and
C operator&amp;(C left, const C &amp; right)
{
    left &amp;= right;
    return left;
}
</code></pre>

<h3 id="boolean">Boolean (logical) operators</h3>
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>logical and</td><td><code>bool operator&amp;&amp;(const C &amp; left, const C &amp; right)</code></td><td>Should be non member</td>
            <td>
                <ul>
                <li>If overloaded will not have short circuit semantics</li>
                <li>May also return other type</li>
                <li>Until C++17 no sequence point holds</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>logical or</td><td><code>bool operator||(const C &amp; left, const C &amp; right)</code></td><td>Should be non member</td>
            <td>
                <ul>
                <li>If overloaded will not have short circuit semantics</li>
                <li>May also return other type</li>
                <li>Until C++17 no sequence point holds</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>logical not</td><td><code>bool C::operator!() const</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>May also return some other type</li>
                </ul>
            </td>
        </tr>
    </tbody>
</table>

<h3 id="comparison">Comparison (relational) operators</h3>
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>equality</td><td><code>bool operator==(const C &amp; left, const C &amp; right)</code></td><td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>inequality</td><td><code>bool operator!=(const C &amp; left, const C &amp; right)</code></td><td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                <li>Implement in terms of <code>operator==</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>less-than</td><td><code>bool operator&lt;(const C &amp; left, const C &amp; right)</code></td><td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>less-or-equal-than</td><td><code>bool operator&lt;=(const C &amp; left, const C &amp; right)</code></td><td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                <li>Implement in terms of <code>operator&gt;</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>greater-than</td><td><code>bool operator&gt;(const C &amp; left, const C &amp; right)</code></td><td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                <li>Implement in terms of <code>operator&lt;</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>greater-than-or-equal</td><td><code>bool operator&gt;=(const C &amp; left, const C &amp; right)</code></td><td>Should be non member</td>
            <td>
                <ul>
                <li>May also compare to other types, if it makes sense, but beware conversion and symmetry issues</li>
                <li>Implement in terms of <code>operator&lt;=</code></li>
                </ul>
            </td>
        </tr>
    </tbody>
</table>
<h4>Examples</h4>
<pre><code>
// inequality
bool operator!=(const C &amp; left, const C &amp; right)
{
    return ! (left == right);
}

// less-or-equal-than
bool operator&lt;=(const C &amp; left, const C &amp; right)
{
    return ! (left &gt; right);
}

// greater-than
bool operator&gt;(const C &amp; left, const C &amp; right)
{
    return right &amp;;lt left;
}

// greater-than-or-equal
bool operator&gt;=(const C &amp; left, const C &amp; right)
{
    return right &lt;= left;
}
</code></pre>

<h3 id="inc_dec">Increment / Decrement operators</h3>
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>pre-increment</td><td><code>C &amp; C::operator++()</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>    
                </ul>
            </td>
        </tr>
        <tr>
            <td>post-increment</td><td><code>C C::operator++(int)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Dummy <code>int</code> param</li>
                <li>Return "old" <code>*this</code></li>
                <li>Implement in terms of operator++()</li>
            </ul>
            </td>
        </tr>
        <tr>
            <td>pre-decrement</td><td><code>C &amp; C::operator--()</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Return <code>*this</code></li>    
                </ul>
            </td>
        </tr>
        <tr>
            <td>post-decrement</td><td><code>C C::operator--(int)</code></td><td>Should be member</td>
            <td>
                <ul>
                <li>Dummy <code>int</code> param</li>
                <li>Return "old" <code>*this</code></li>
                <li>Implement in terms of operator--()</li>
            </ul>
            </td>
        </tr>
    </tbody>
</table>
<h4>Examples</h4>
<pre><code>
// pre-increment
C &amp; C::operator++()
{
    // DO INCREMENT HERE...
    return *this;
}

// post-increment
C C::operator++(int)
{
    C old{*this};
    ++*this;
    return old;
}
</code></pre>

<h3 id="i_o">I/O streams operators</h3>
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>stream extraction</td><td><code>std::ostream &amp; operator&lt;&lt;(std::ostream &amp; os, const C &amp; c)</code></td><td>Must not be member</td>
            <td>
                <ul>
                <li>Should return <code>os</code></li>
                <li>Restore stream state if modified</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>stream insertion</td><td><code>std::istream &amp; operator&gt;&gt;(std::istream &amp; is, C &amp; c)</code></td><td>Must not be member</td>
            <td>
                <ul>
                <li>Should return <code>is</code></li>
                <li>Set stream state if errors</li>
                </ul>
            </td>
        </tr>
    </tbody>
</table>

<h3 id="memory">Memory management operators</h3>
<mark>TODO: intro</mark>

<h3 id="other">Other operators</h3>
<table>
    <thead><tr><th>Operator</th><th>Typical signature</th><th>Class member?</th><th>Notes</th></tr></thead>
    <tbody>
        <tr>
            <td>function call</td><td><code>Y C::operator()(X x) const</code></td><td>Must be member</td>
            <td>
                <ul>
                <li>May be <code>const</code>, or not</li>
                <li>Return type and (multiple) params as needed</li>
                <li>Multiple overloads possible</li>
                <li>Function objects should be cheap to copy</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>comma</td><td><code>Y operator,(const X &amp; left, const Y &amp; right)</code></td><td>Should be non-member</td>
            <td>
                <ul>
                <li>May return whatever makes sense</li>
                <li>Beware: no sequence point holds, so operands may be evaluated in any order</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>conversion</td><td><code>C::operator X() const</code></td><td>May be member or not</td>
            <td>
                <ul>
                <li>Since C++ 11 may be <code>explicit</code></li>
                <li>Return type will be <code>X</code></li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>copy assignment</td><td><code>C &amp; operator=(const C &amp; other)</code></td><td>Must be member</td>
            <td>
                <ul>
                    <li>Return <code>*this</code></li>
                    <li>Should free the resources held by <code>*this</code></li>
                    <li>Should make a deep copy of the resources held by <code>other</code></li> 
                </ul>
            </td>
        </tr>
        <tr>
            <td>move assignment</td><td><code>C &amp; operator=(const C &amp;&amp; other)</code></td><td>Must be member</td>
            <td>
                <ul>
                    <li>Return <code>*this</code></li>
                    <li>Should free the resources held by <code>*this</code></li>
                    <li>Should "steal" the resources held by <code>other</code> and pass them to <code>*this</code></li>
                    <li>Should leave other in a "null-like" but destructible state</li>
                </ul>
            </td>
        </tr>
    </tbody>
</table>
</body>